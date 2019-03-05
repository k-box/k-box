<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChangeUserLanguagePreferenceControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function languages_provider()
    {
        return [
            [Capability::$ADMIN, 'ru'],
            [Capability::$PROJECT_MANAGER, 'de'],
            [Capability::$PARTNER, 'tg'],
            [Capability::$PARTNER, 'en'],
        ];
    }

    /**
     * @dataProvider languages_provider
     */
    public function test_language_preference_can_be_changed_for_user($capabilities, $value)
    {
        $user = tap(factory(User::class)->create(), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
        });
        
        $response = $this->actingAs($user)->put(route('profile.language.update'), [
            User::OPTION_LANGUAGE => $value,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.index'));

        $response->assertSessionHas('flash_message', trans('profile.messages.language_changed'));

        $this->assertEquals($user->getOption(User::OPTION_LANGUAGE)->value, $value);
    }

    public function test_language_preference_do_not_accept_invalid_language()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $response = $this->actingAs($user)->json('PUT', route('profile.language.update'), [
            User::OPTION_LANGUAGE => 'uz',
            '_token' => csrf_token()
        ]);
            
        $response->assertStatus(422);

        $response->assertJsonStructure([
            'language',
        ]);
    }

    public function test_language_preference_refuses_unknown_object()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $response = $this->actingAs($user)->json('PUT', route('profile.language.update'), [
            'User::OPTION_LANGUAGE' => 'uz',
            '_token' => csrf_token()
        ]);
            
        $response->assertStatus(422);

        $response->assertJsonStructure([
            'language',
        ]);
    }
}
