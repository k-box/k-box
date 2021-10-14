<?php

namespace Tests\Feature\Identities;

use Illuminate\Support\Facades\Session;
use KBox\Capability;
use KBox\Invite;
use KBox\User;
use SocialiteProviders\GitLab\Provider;
use SocialiteProviders\Manager\OAuth2\User as OauthUser;
use KBox\Facades\Identity as IdentityFacade;
use Mockery;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_social_registration_forbidden_if_registration_is_disabled()
    {
        config(['registration.enable' => false]);

        $response = $this->get(route('oneofftech::register.provider', ['provider' => 'gitlab']));

        $response->assertForbidden();
    }
    
    public function test_social_option_not_visible_when_disabled()
    {
        config([
            'registration.enable' => true,
            'identities.providers' => null,
        ]);

        $response = $this->get(route('register'));

        $response->assertOk();

        $response->assertDontSee('Register via Gitlab');
    }
    
    public function test_social_registration_presented_if_enabled()
    {
        config([
            'registration.enable' => true,
            'identities.providers' => 'gitlab,dropbox',
        ]);

        $response = $this->get(route('register'));

        $response->assertOk();

        $response->assertSee('Register via Gitlab');
        $response->assertSee('Register via Dropbox');
    }
    
    public function test_social_registration_respect_invite_if_present()
    {
        config([
            'registration.enable' => true,
            'identities.providers' => 'gitlab',
        ]);

        $this->withoutExceptionHandling();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $invite = factory(Invite::class)->create([
            'creator_id' => $user->id
        ]);
        
        $driverMock = Mockery::mock(Provider::class)->makePartial();

        $oauthFakeUser = (new OauthUser())->map([
            'id'       => 'U1',
            'nickname' => 'User',
            'name'     => 'User',
            'email'    => $invite->email,
            'avatar'   => 'https://randomuser.me/api/portraits/med/men/75.jpg',
            'token'   => 'T1',
        ]);
        
        $driverMock->shouldReceive('user')->andReturn($oauthFakeUser);

        $driverMock->shouldReceive('redirectUrl')->andReturn($driverMock);

        IdentityFacade::shouldReceive('driver')->with('gitlab')->andReturn($driverMock);

        Session::put('_oot.identities.attributes', json_encode([
            'invite' => $invite->token,
        ]));

        $response = $this->get(route('oneofftech::register.callback', ['provider' => 'gitlab']));

        $response->assertRedirect('/');

        $created = User::whereEmail($invite->email)->first();

        $this->assertNotNull($created);

        $this->assertAuthenticatedAs($created);

        $updatedInvite = $invite->fresh();
        $this->assertTrue($updatedInvite->wasAccepted());
        $this->assertEquals($created->getKey(), $updatedInvite->user_id);
    }
}
