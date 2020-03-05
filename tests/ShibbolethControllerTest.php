<?php

use App\User;
use Orchestra\Testbench\TestCase;
use StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController;
use StudentAffairsUwm\Shibboleth\Tests\Stubs\Setup;

class ShibbolethControllerTest extends TestCase
{
    use Setup;

    public function test_creates_user()
    {
        $getUser = function () {
            return User::where('email', 'user@example.org')->first();
        };

        $this->assertEmpty($getUser());

        User::create([
            'name' => 'user',
            'email' => 'user@example.org',
            'password' => 'password',
        ]);

        $this->assertInstanceOf(User::class, $getUser());

        (new ShibbolethController)->idpAuthenticate();

        $this->assertSame('100000001', $getUser()->emplid);
    }
}
