<?php

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;
use StudentAffairsUwm\Shibboleth\Tests\Stubs\Setup;
use App\User;

class MigrateDatabaseTest extends TestCase
{
    use Setup;

    /**
     * Test running migration.
     *
     * @test
     */
    public function testRunningMigration()
    {
        $now = Carbon::now();

        User::create([
            'first_name' => 'Jeff',
            'last_name'  => 'Puckett',
            'email'      => 'jeff@jeffpuckett.com',
            'password'   => 'password',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $user = DB::table('users')->where('id', '=', 1)->first();

        $this->assertSame('jeff@jeffpuckett.com', $user->email);
    }
}
