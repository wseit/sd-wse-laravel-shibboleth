<?php

namespace Jhu\Wse\LaravelShibboleth\Controllers;

use Illuminate\Auth\GenericUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Jhu\Wse\LaravelShibboleth\Exceptions\MissingUserAuthenticationFieldException;
use JWTAuth;

class ShibbolethController extends Controller
{
    /**
     * Service Provider
     * @var Shibalike\SP
     */
    private $sp;

    /**
     * Identity Provider
     * @var Shibalike\IdP
     */
    private $idp;

    /**
     * Configuration
     * @var Shibalike\Config
     */
    private $config;

    /**
     * Constructor
     */
    public function __construct(GenericUser $user = null)
    {
        if (config('shibboleth.emulate_idp') === true) {
            $this->config = new \Shibalike\Config();
            $this->config->idpUrl = url('/emulated/idp');

            $stateManager = $this->getStateManager();

            $this->sp = new \Shibalike\SP($stateManager, $this->config);
            $this->sp->initLazySession();

            $this->idp = new \Shibalike\IdP($stateManager, $this->getAttrStore(), $this->config);
        }

        $this->user = $user;
    }

    /**
     * Create the session, send the user away to the IDP
     * for authentication.
     */
    public function login()
    {
        if (config('shibboleth.emulate_idp') === true) {
            return Redirect::to(action('\\' . __class__ . '@emulateLogin')
                . '?target=' . action('\\' . __class__ . '@idpAuthenticate'));
        }

        return Redirect::to('https://' . Request::server('SERVER_NAME')
            . ':' . Request::server('SERVER_PORT') . config('shibboleth.idp_login')
            . '?target=' . action('\\' . __class__ . '@idpAuthenticate'));
    }

    /**
     * Setup authentication based on returned server variables
     * from the IdP.
     */
    public function idpAuthenticate()
    {
        //Get the user mapping
        $userMap = config('shibboleth.user');

        //Make sure it makes sense
        if (!is_array($userMap) || count($userMap) === 0) {
            throw new \Exception('You must provide at least one mapping of user attributes to server variables in shibboleth.user config');
        }

        //Create an array we can use with keys of the user model and the value from the idp serve
        $modelToServerValuesMap = [];
        foreach ($userMap as $local => $server) {
            $modelToServerValuesMap[$local] = $this->getServerVariable($server);
        }

        //Remove nulls
        $modelToServerValuesMap = array_filter($modelToServerValuesMap);

        //Get the field to use for authentication, defaulting to email
        $userAuthenticationField = config('shibboleth.user_authentication_field', 'email');

        //If the key at $map[$userAuthenticationField] doesn't exist OR is null... report an error
        if (!isset($modelToServerValuesMap[$userAuthenticationField]) || $modelToServerValuesMap[$userAuthenticationField] === null) {
            throw new MissingUserAuthenticationFieldException(
                message: 'The user authentication field "'.$userAuthenticationField.'" was missing from the server fields when trying to authentication.',
                context: [
                    'modelToServerValuesMap' => $modelToServerValuesMap
                ]
            );
        }

        //Get the class name for the user model
        $userModelClass = config('auth.providers.users.model', 'App\Model\User');

        //Build up the credentials
        $credentials = [
            $userAuthenticationField => $modelToServerValuesMap[$userAuthenticationField]
        ];

        //Attempt the login
        if (Auth::attempt($credentials, true)) {
            $user = $userModelClass::where($userAuthenticationField, '=', $modelToServerValuesMap[$userAuthenticationField])->first();

            // Update the model as necessary
            if (config('shibboleth.update_users')) {
                $user->update($modelToServerValuesMap);
            }
        } elseif (config('shibboleth.add_new_users', true)) {
            //If login failed and we are adding new users, attempt to add
            $modelToServerValuesMap['password'] = 'shibboleth';
            $user = $userModelClass::create($modelToServerValuesMap);
            Auth::login($user);
        } else {
            //user not found in this system, complain and show an access denied message
            Log::notice('User successfully logged in via Shibboleth but could not be matched to a user in this system.', $modelToServerValuesMap);
            return abort(403);
        }

        Session::regenerate();

        $route = config('shibboleth.authenticated');

        if (config('shibboleth.jwtauth') === true) {
            $route .= $this->tokenizeRedirect($user, ['auth_type' => 'idp']);
        }

        return redirect()->intended($route);
    }

    /**
     * Destroy the current session and log the user out, redirect them to the main route.
     */
    public function destroy()
    {
        Auth::logout();
        Session::flush();

        if (config('shibboleth.jwtauth') === true) {
            $token = JWTAuth::parseToken();
            $token->invalidate();
        }

        if (config('shibboleth.emulate_idp') === true) {
            return Redirect::to(action('\\' . __class__ . '@emulateLogout'));
        }

        return Redirect::to('https://' . Request::server('SERVER_NAME') . config('shibboleth.idp_logout'));
    }

    /**
     * Emulate a login via Shibalike
     */
    public function emulateLogin()
    {
        $from = (Request::input('target') != null) ? Request::input('target') : $this->getServerVariable('HTTP_REFERER');

        $this->sp->makeAuthRequest($from);
        $this->sp->redirect();
    }

    /**
     * Emulate a logout via Shibalike
     */
    public function emulateLogout()
    {
        $this->sp->logout();

        $referer = $this->getServerVariable('HTTP_REFERER');

        die("Goodbye, fair user. <a href='$referer'>Return from whence you came</a>!");
    }

    /**
     * Emulate the 'authentication' via Shibalike
     */
    public function emulateIdp()
    {
        $data = [];

        if (Request::input('username') != null) {
            $username = (Request::input('username') === Request::input('password')) ?
                Request::input('username') : '';

            $userAttrs = $this->idp->fetchAttrs($username);
            if ($userAttrs) {
                $this->idp->markAsAuthenticated($username);
                $this->idp->redirect(route('shibboleth-authenticate'));
            }

            $data['error'] = 'Incorrect username and/or password';
        }

        return view('shibalike::IdpLogin', $data);
    }

    /**
     * Function to get an attribute store for Shibalike
     */
    private function getAttrStore()
    {
        return new \Shibalike\Attr\Store\ArrayStore(config('shibboleth.emulate_idp_users'));
    }

    /**
     * Gets a state manager for Shibalike
     */
    private function getStateManager()
    {
        $session = \UserlandSession\SessionBuilder::instance()
            ->setSavePath(sys_get_temp_dir())
            ->setName('SHIBALIKE_BASIC')
            ->build();

        return new \Shibalike\StateManager\UserlandSession($session);
    }

    /**
     * Wrapper function for getting server variables.
     * Since Shibalike injects $_SERVER variables Laravel
     * doesn't pick them up. So depending on if we are
     * using the emulated IdP or a real one, we use the
     * appropriate function.
     */
    private function getServerVariable($variableName)
    {
        if (config('shibboleth.emulate_idp') === true) {
            return isset($_SERVER[$variableName]) ?
                $_SERVER[$variableName] : null;
        }

        $variable = Request::server($variableName);

        return (!empty($variable)) ?
            $variable : Request::server('REDIRECT_' . $variableName);
    }

    /*
     * Simple function that allows configuration variables
     * to be either names of views, or redirect routes.
     */
    private function viewOrRedirect($view)
    {
        return (View::exists($view)) ? view($view) : Redirect::to($view);
    }

    /**
     * Uses JWTAuth to tokenize the user and returns a URL query string.
     *
     * @param  App\User $user
     * @param  array $customClaims
     * @return string
     */
    private function tokenizeRedirect($user, $customClaims)
    {
        // This is where we used to setup a session. Now we will setup a token.
        $token = JWTAuth::fromUser($user, $customClaims);

        // We need to pass the token... how?
        // Let's try this.
        return "?token=$token";
    }
}
