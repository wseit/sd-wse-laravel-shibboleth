Laravel Shibboleth Service Provider
===================================

This package provides an easy way to implement Shibboleth Authentication for
Laravel 5.4

[![Code Climate][3]][2]
[![Build Status][12]][11]

## Features ##

- Compatibility with Laravel 5.4
- Includes User and Group model examples
- Ability to *emulate* an IdP (via [mrclay/shibalike][13])

## Pre-Requisites ##

In order to use this plugin, we assume you already have a pre-existing
Shibboleth SP and Shibboleth IdP configured. This does not (and will not) go
into explaining how to set that up.

## Installation ##

If you would like to allow local registration as well as authenticate shibboleth
users, then use laravel's built-in auth system.

    $ php artisan make:auth

Use [composer][1] to require the latest release into your project:

    $ composer require saitswebuwm/shibboleth

Then, append the following line inside your `/config/app.php` file within the
`Providers` array.

```php
StudentAffairsUwm\Shibboleth\ShibbolethServiceProvider::class,
```

Publish the default configuration file, migrations, and views:

    $ php artisan vendor:publish --provider="StudentAffairsUwm\Shibboleth\ShibbolethServiceProvider"

Run the database migrations:

    $ php artisan migrate

Once the migrations have run successfully, change the driver to `shibboleth` in
your `/config/auth.php` file.

```php
'providers' => [
    'users' => [
        'driver' => 'shibboleth',
        'model'  => App\User::class,
    ],
],
```

Add Entitlement [`belongsToMany` relationship][14] to your User model:

```php
use StudentAffairsUwm\Shibboleth\Entitlement;

class User extends Model
{
    // ...

    /**
     * The entitlements that belong to the user.
     */
    public function entitlements()
    {
        return $this->belongsToMany(Entitlement::class);
    }
}
```

This assumes you have a `users` table with an integer primary key of `id`
so if you have a custom configuration, then you will need to manually edit the
[`database/migrations/2017_02_24_100000_create_entitlement_user_table.php`][15]
in order to match your custom table name and foreign key relationship.

Now users may login via Shibboleth by going to `https://example.com/idp`
and logout using `https://example.com/logout`

You may configure server variable mappings in `config/shibboleth.php` such as
the user's first name, last name, entitlements, etc. You can take a look at them
by reading what's been populated into the `$_SERVER` variable after authentication.

```php
<?php print_r($_SERVER);
```

## Groups and Entitlements ##

The old 1.x versions required a base `App\Group` model, but this interfered with
native authorization policies not dependent on Shibboleth. Now a new model with
database migrations exists called `StudentAffairsUwm\Shibboleth\Entitlement`
which allows access control mechanisms to be built around the extraneous source.
Synchronization of these objects are independent of native groups, so
authorization can be delegated with inherent separation of concerns.

You will need to add entitlements in which you are interested to the database.
This can easily be accomplished with artisan tinker.

    $ php artisan tinker

```php
>>> $entitlement = new StudentAffairsUwm\Shibboleth\Entitlement;
=> StudentAffairsUwm\Shibboleth\Entitlement {#689}
>>> $entitlement->name = 'urn:mace:uark.edu:ADGroups:walton:Groups:linux02_sudoers'
=> "urn:mace:uark.edu:ADGroups:walton:Groups:linux02_sudoers"
>>> $entitlement->save()
=> true
```

Now you can draft [policies and gates][16] around these entitlements.

## JWTAuth Tokens ##

If you're taking advantage of token authentication with [tymon/jwt-auth][4] then
set this variable in your `.env`

    JWTAUTH=true

## Looking for Laravel 5.0 or 4? ##

Laravel 5.0 should be compatible up to tag 1.1.1

We have stopped development on the Laravel 4 version of this plugin for now.
We are welcoming pull requests, however!
Feel free to use any tag below 1.0.0 for Laravel 4 compatible versions.

[1]:https://getcomposer.org/
[2]:https://codeclimate.com/github/StudentAffairsUWM/Laravel-Shibboleth-Service-Provider
[3]:https://codeclimate.com/github/StudentAffairsUWM/Laravel-Shibboleth-Service-Provider/badges/gpa.svg
[4]:https://github.com/tymondesigns/jwt-auth
[11]:https://travis-ci.org/uawcob/Laravel-Shibboleth-Service-Provider
[12]:https://travis-ci.org/uawcob/Laravel-Shibboleth-Service-Provider.svg?branch=master
[13]:https://github.com/mrclay/shibalike
[14]:https://laravel.com/docs/5.4/eloquent-relationships#many-to-many
[15]:./src/database/migrations/2017_02_24_100000_create_entitlement_user_table.php
[16]:https://laravel.com/docs/5.4/authorization
