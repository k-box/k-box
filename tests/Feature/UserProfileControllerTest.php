<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserProfileControllerTest extends TestCase
{
    use DatabaseTransactions;

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
    public function test_profile_page_is_reachable($capabilities)
    {
        $user = tap(factory(User::class)->create(), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
        });
        
        $response = $this->actingAs($user)->get(route('profile.index'));
            
        $response->assertSuccessful();
        $response->assertViewIs('profile.user');
    }
    
    public function test_user_can_change_name()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $old_name = $user->name;
        $new_name = 'albert';
        
        $response = $this->from(route('profile.index'))->actingAs($user)->put(route('profile.update'), [
            'name' => $new_name,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.index'));

        $response->assertSessionHas('flash_message', trans('profile.messages.info_changed'));

        $this->assertNotEquals($old_name, $user->fresh()->name);
        $this->assertEquals($new_name, $user->fresh()->name);
    }
    
    public function test_user_cannot_change_name_to_another_existing_username()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        $existing_user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $response = $this->from(route('profile.index'))->actingAs($user)->put(route('profile.update'), [
            'name' => $existing_user->name,
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHasErrors('name');
    }
    
    public function test_user_can_change_organization()
    {
        $user = tap(factory(User::class)->create([
            'organization_name' => 'Org',
            'organization_website' => 'www.org.com',
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $old_organization_name = $user->organization_name;
        $old_organization_website = $user->organization_website;
        
        $response = $this->from(route('profile.index'))->actingAs($user)->put(route('profile.update'), [
            'organization_name' => 'New organization',
            'organization_website' => 'https://www.org.new',
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.index'));
        
        $saved_user = $user->fresh();

        $this->assertNotEquals($old_organization_name, $user->organization_name);
        $this->assertNotEquals($old_organization_website, $user->organization_website);
        $this->assertEquals('New organization', $user->organization_name);
        $this->assertEquals('https://www.org.new', $user->organization_website);
    }
    
    public function test_user_can_clear_organization_details()
    {
        $user = tap(factory(User::class)->create([
            'organization_name' => 'Org',
            'organization_website' => 'www.org.com',
        ]), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $old_organization_name = $user->organization_name;
        $old_organization_website = $user->organization_website;
        
        $response = $this->from(route('profile.index'))->actingAs($user)->put(route('profile.update'), [
            'organization_name' => '',
            'organization_website' => '',
            '_token' => csrf_token()
        ]);
            
        $response->assertRedirect(route('profile.index'));
        
        $saved_user = $user->fresh();

        $this->assertNotEquals($old_organization_name, $user->organization_name);
        $this->assertNotEquals($old_organization_website, $user->organization_website);
        $this->assertEmpty($user->organization_name);
        $this->assertEmpty($user->organization_website);
    }
}
