<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\PasswordReset;

class UserPasswordControllerTest extends TestCase
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
    public function test_password_change_page_is_reachable($capabilities)
    {
        $user = tap(factory(User::class)->create(), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
            $u->markEmailAsVerified();
        });
        
        $response = $this->actingAs($user)->get(route('profile.password.index'));
            
        $response->assertSuccessful();
        $response->assertViewIs('profile.password');
    }
    
    public function test_password_change_possible_only_if_email_is_verified()
    {
        $user = tap(factory(User::class)->create([
            'email_verified_at' => null
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $response = $this->actingAs($user)->get(route('profile.password.index'));
            
        $response->assertRedirect(route('verification.notice'));
    }
    
    public function test_user_can_change_password()
    {
        Event::fake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $current_password = $user->password;
        $new_password = 'the-new-password';
        
        $response = $this->from(route('profile.password.index'))->actingAs($user)->put(route('profile.password.update'), [
            'password' => $new_password,
            'password_confirm' => $new_password,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.password.index'));

        $response->assertSessionHas('flash_message', trans('profile.messages.password_changed'));

        Event::assertDispatched(PasswordReset::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });

        $this->assertNotEquals($current_password, $user->fresh()->password);
    }
    
    public function test_change_refused_if_new_password_do_not_pass_validation()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
            $u->markEmailAsVerified();
        });

        $new_password = '1';
        
        $response = $this->from(route('profile.password.index'))->actingAs($user)->put(route('profile.password.update'), [
            'password' => $new_password,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.password.index'));
        $response->assertSessionHasErrors('password');
    }
}
