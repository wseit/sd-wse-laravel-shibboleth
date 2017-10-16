<?php

use Orchestra\Testbench\TestCase;
use StudentAffairsUwm\Shibboleth\Tests\Stubs\Setup;
use App\User;
use StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController;

class ShibbolethControllerTest extends TestCase
{
    use Setup;

    public function test_creates_user()
    {
        $getJeff = function(){
            return User::where('email', 'jeff@example.org')->first();
        };

        $this->assertEmpty($getJeff());

        User::create([
            'name' => 'jeff',
            'email' => 'jeff@example.org',
            'password' => 'password',
        ]);

        $this->assertInstanceOf(User::class, $getJeff());

        (new ShibbolethController)->idpAuthenticate();

        $this->assertSame('100000001', $getJeff()->student_id);
    }
}
