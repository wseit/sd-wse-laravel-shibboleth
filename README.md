# Laravel Shibboleth Service Provider

This package provides Shibboleth authentication for Laravel.

For development, it can _emulate_ an IdP (via [mrclay/shibalike][13]).

[![Build Status][12]][11] [![Code Climate][3]][2] [![Code Coverage][8]][7]

## Installation

Use [composer][1] to require the latest release into your project:

    composer require uabookstores/laravel-shibboleth

If you you would like to use the emulated IdP via shibalike, then you will need
to manually register it on any version - this is not automatically loaded.

```php
StudentAffairsUwm\Shibboleth\ShibalikeServiceProvider::class,
```

_Note_ that the password is the same as the username for shibalike.

Publish the default configuration file:

    php artisan vendor:publish --provider="StudentAffairsUwm\Shibboleth\ShibbolethServiceProvider"

Optionally, you can also publish the views for the shibalike emulated IdP login:

    php artisan vendor:publish --provider="StudentAffairsUwm\Shibboleth\ShibalikeServiceProvider"

Change the driver to `shibboleth` in your `config/auth.php` file.

```php
'providers' => [
    'users' => [
        'driver' => 'shibboleth',
        'model'  => App\Models\User::class,
    ],
],
```

Now users may login via Shibboleth by going to
`https://example.com/shibboleth-login` and logout using
`https://example.com/shibboleth-logout` so you can provide a custom link or
redirect based on email address in the login form.

```php
@if (Auth::guest())
    <a href="/shibboleth-login">Login</a>
@else
    <a href="/shibboleth-logout">
        Logout {{ Auth::user()->name }}
    </a>
@endif
```

You may configure server variable mappings in `config/shibboleth.php` such as
the user's first name, last name, entitlements, etc. You can take a look at them
by reading what's been populated into the `$_SERVER` variable after
authentication.

```php
<?php print_r($_SERVER);
```

Mapped values will be synced to the user table upon successful authentication.

## Authorization

You can check for an entitlement string of the current user statically:

```php
$entitlement = 'urn:mace:uark.edu:ADGroups:Computing Services:Something';

if (Entitlement::has($entitlement)) {
    // authorize something
}
```

Now you can draft [policies and gates][16] around these entitlements.

## Local Users

This was designed to work side-by-side with the native authentication system for
projects where you want to have both Shibboleth and local users. If you would
like to allow local registration as well as authenticate Shibboleth users, then
use laravel's built-in auth system.

    php artisan make:auth

## JWTAuth Tokens

If you're taking advantage of token authentication with [tymon/jwt-auth][4] then
set this variable in your `.env`

    JWTAUTH=true

[1]: https://getcomposer.org/
[2]: https://codeclimate.com/github/razorbacks/laravel-shibboleth
[3]: https://codeclimate.com/github/razorbacks/laravel-shibboleth/badges/gpa.svg
[4]: https://github.com/tymondesigns/jwt-auth
[7]: https://codecov.io/gh/razorbacks/laravel-shibboleth/branch/master
[8]: https://img.shields.io/codecov/c/github/razorbacks/laravel-shibboleth/master.svg
[11]: https://travis-ci.org/razorbacks/laravel-shibboleth
[12]: https://travis-ci.org/razorbacks/laravel-shibboleth.svg?branch=master
[13]: https://github.com/mrclay/shibalike
[14]: https://laravel.com/docs/eloquent-relationships#many-to-many
[15]: ./src/database/migrations/2017_02_24_100000_create_entitlement_user_table.php
[16]: https://laravel.com/docs/authorization
