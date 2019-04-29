<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_user_can_see_register_page()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $response->assertSee(trans('auth.create_account'));
    }

    public function test_user_can_register()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'email' => 'new-user@example.com',
            'password' => 'super-secure-password',
            'password_confirmation' => 'super-secure-password',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user_created = User::where('email', 'new-user@example.com')->first();

        $this->assertEquals('new-user', $user_created->name);

        Notification::assertSentTo(
            [$user_created],
            VerifyEmail::class
        );
    }
}
