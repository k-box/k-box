<?php

namespace Tests\Unit\Commands;

use Artisan;
use KBox\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DmsCreateAdminUserCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_user_is_created_and_reset_link_is_returned()
    {
        $email = 'admin@kbox.local';

        $exitCode = Artisan::call('create-admin', [
            'email' => $email,
            '--no-interaction' => true
        ]);
        $output = Artisan::output();
        $this->assertEquals(0, $exitCode);
        
        $this->assertRegExp('/Administrator(.*)created/', $output);
        $this->assertRegExp('/Set\ a\ password/', $output);
        $this->assertRegExp('/http(.*)\/password\/reset\/(.*)?email='.urlencode($email).'/', $output);

        $this->assertNotNull(User::findByEmail($email));
    }

    public function test_admin_user_is_created_and_reset_link_is_returned_if_empty_password_is_specified()
    {
        $email = 'admin@kbox.local';

        $exitCode = Artisan::call('create-admin', [
            'email' => $email,
            '--password' => '',
            '--no-interaction' => true
        ]);
        $output = Artisan::output();
        $this->assertEquals(0, $exitCode);
        
        $this->assertRegExp('/Administrator(.*)created/', $output);
        $this->assertRegExp('/Set\ a\ password/', $output);
        $this->assertRegExp('/http(.*)\/password\/reset\/(.*)?email='.urlencode($email).'/', $output);

        $this->assertNotNull(User::findByEmail($email));
    }

    public function test_admin_user_is_created_using_input_password()
    {
        $email = 'admin@kbox.local';

        $exitCode = Artisan::call('create-admin', [
            'email' => $email,
            '--password' => 'A password',
        ]);
        $output = Artisan::output();
        $this->assertEquals(0, $exitCode);
        
        $this->assertRegExp('/Administrator(.*)created/', $output);
        $this->assertRegExp('/chosen\ password/', $output);

        $this->assertNotNull(User::findByEmail($email));
    }

    public function test_admin_user_created_and_show_generated_password()
    {
        $email = 'admin@kbox.local';

        $exitCode = Artisan::call('create-admin', [
            'email' => $email,
            '--no-interaction' => true,
            '--show' => true,
        ]);
        $output = Artisan::output();
        $this->assertEquals(0, $exitCode);
        
        $this->assertRegExp('/Administrator(.*)created/', $output);
        $this->assertRegExp('/password(.*)generated(.*):\ (.*){8}\ /', $output);

        $this->assertNotNull(User::findByEmail($email));
    }

    public function test_admin_user_is_not_created_if_already_exist()
    {
        $email = 'admin@kbox.local';

        User::create([
            'name' => 'admin',
            'email' => $email,
            'password' => 'secure-code'
        ]);

        $exitCode = Artisan::call('create-admin', [
            'email' => $email,
            '--no-interaction' => true,
        ]);
        $this->assertEquals(2, $exitCode);
    }

    public function test_invalid_email_blocks_the_user_creation()
    {
        $email = 'admin';

        $exitCode = Artisan::call('create-admin', [
            'email' => $email,
            '--no-interaction' => true,
        ]);
        $this->assertEquals(3, $exitCode);
    }
}
