<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserEmailControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function capabilities_provider()
    {
        return [
            [Capability::$ADMIN],
            [Capability::$PROJECT_MANAGER],
            [Capability::$PARTNER],
            [Capability::$GUEST],
        ];
    }

    /**
     * @dataProvider capabilities_provider
     */
    public function test_email_change_page_is_reachable($capabilities)
    {
        $user = tap(factory(User::class)->create(), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
        });
        
        $response = $this->actingAs($user)->get(route('profile.email.index'));
            
        $response->assertSuccessful();
        $response->assertViewIs('profile.email');
    }
    
    public function test_user_can_change_email()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $current_email = $user->email;
        $new_email = 'albert@example.website';
        
        $response = $this->from(route('profile.email.index'))->actingAs($user)->put(route('profile.email.update'), [
            'email' => $new_email,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.email.index'));

        $response->assertSessionHas('flash_message', trans('profile.messages.mail_changed'));

        $this->assertNotEquals($current_email, $user->fresh()->email);
    }
    
    public function test_change_refused_if_new_email_do_not_pass_validation()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $response = $this->from(route('profile.email.index'))->actingAs($user)->put(route('profile.email.update'), [
            'email' => $user->email,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.email.index'));
        $response->assertSessionHasErrors('email');
    }
}
