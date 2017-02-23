<?php

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;
use StudentAffairsUwm\Shibboleth\Tests\Stubs\Setup;

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
        \DB::table('users')->insert([
            'first_name' => 'Jeff',
            'last_name'  => 'Puckett',
            'email'      => 'jeff@jeffpuckett.com',
            'password'   => \Hash::make('456'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $users = \DB::table('users')->where('id', '=', 1)->first();
        $this->assertEquals('jeff@jeffpuckett.com', $users->email);
        $this->assertTrue(\Hash::check('456', $users->password));
    }
}
