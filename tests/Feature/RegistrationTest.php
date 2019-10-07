<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Auth\Registration;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_user_can_see_register_page()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $response->assertSee(trans('auth.create_account'));
    }

    public function test_registration_enabled()
    {
        config(['registration.enable' => "true"]);

        $this->assertTrue(Registration::isEnabled());
    }

    public function test_registration_disabled()
    {
        config(['registration.enable' => "false"]);

        $this->assertFalse(Registration::isEnabled());
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

    public function test_user_cannot_register()
    {
        $this->withoutExceptionHandling();

        config(['registration.enable' => false]);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("User registration is not active on this instance");

        $response = $this->post('/register', [
            'email' => 'new-user@example.com',
            'password' => 'super-secure-password',
            'password_confirmation' => 'super-secure-password',
        ]);
        
        $user_created = User::where('email', 'new-user@example.com')->first();

        $this->assertNull($user_created);
    }

    public function test_user_cannot_register_without_invite()
    {
        config(['registration.invite_required' => true]);

        $response = $this->from(route('register'))
            ->post(route('register'), [
            'email' => 'new-user@example.com',
            'password' => 'super-secure-password',
            'password_confirmation' => 'super-secure-password',
        ]);

        $response->assertRedirect(route('register'));

        $response->assertSessionHasErrors("invite");

        $user_created = User::where('email', 'new-user@example.com')->first();

        $this->assertNull($user_created);
    }
}
