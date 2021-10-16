<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use KBox\Events\EmailChanged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class UserEmailControllerTest extends TestCase
{
    public function capabilities_provider()
    {
        return [
            [Capability::$ADMIN],
            [Capability::$PROJECT_MANAGER],
            [Capability::$PARTNER],
            [[Capability::RECEIVE_AND_SEE_SHARE]],
        ];
    }

    /**
     * @dataProvider capabilities_provider
     */
    public function test_email_change_page_is_reachable($capabilities)
    {
        $user = tap(User::factory()->create(), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
        });
        
        $response = $this->actingAs($user)->get(route('profile.email.index'));
            
        $response->assertSuccessful();
        $response->assertViewIs('profile.email');
    }
    
    public function test_user_can_change_email()
    {
        Notification::fake();
        Event::fake();

        $user = tap(User::factory()->partner()->create(), function ($u) {
            $u->markEmailAsVerified();
        });

        $current_email = $user->email;
        $new_email = 'albert@example.website';
        
        $response = $this->from(route('profile.email.index'))->actingAs($user)->put(route('profile.email.update'), [
            'email' => $new_email,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('verification.notice'));

        $response->assertSessionHas('flash_message', trans('profile.messages.mail_changed'));

        $this->assertNotEquals($current_email, $user->fresh()->email);
        $this->assertFalse($user->fresh()->hasVerifiedEmail(), "email verification status not reset");

        Event::assertDispatched(EmailChanged::class, function ($e) use ($user, $current_email, $new_email) {
            return $e->user->id === $user->id &&
                   $e->from === $current_email &&
                   $e->to === $new_email;
        });

        Notification::assertSentTo(
            [$user],
            VerifyEmail::class
        );
    }
    
    public function test_change_refused_if_new_email_do_not_pass_validation()
    {
        $user = User::factory()->partner()->create();
        
        $response = $this->from(route('profile.email.index'))->actingAs($user)->put(route('profile.email.update'), [
            'email' => $user->email,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.email.index'));
        $response->assertSessionHasErrors('email');
    }
}
