<?php namespace StudentAffairsUwm\Shibboleth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Route;

class ShibbolethServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (env('JWTAUTH')) {
            $this->app->register('Tymon\JWTAuth\Providers\JWTAuthServiceProvider');
            $loader = AliasLoader::getInstance();
            $loader->alias('JWTAuth', 'Tymon\JWTAuth\Facades\JWTAuth');
            $loader->alias('JWTFactory', 'Tymon\JWTAuth\Facades\JWTFactory');
        }

        $this->app['auth']->provider('shibboleth', function ($app) {
            return new Providers\ShibbolethUserProvider($app['config']['auth.providers.users.model']);
        });

        // Publish the configuration, migrations, and views
        $this->publishes([
            __DIR__ . '/../../config/shibboleth.php' => config_path('shibboleth.php'),
            __DIR__ . '/../../database/migrations/2017_02_24_000000_create_entitlements_table.php'  => base_path('/database/migrations/2017_02_24_000000_create_entitlements_table.php'),
            __DIR__ . '/../../database/migrations/2017_02_24_100000_create_entitlement_user_table.php'  => base_path('/database/migrations/2017_02_24_100000_create_entitlement_user_table.php'),
            __DIR__ . '/../../resources/views/'      => base_path('/resources/views'),
        ]);

        Route::group(['middleware' => 'web'], function () {
            require __DIR__ . '/../../routes.php';
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
