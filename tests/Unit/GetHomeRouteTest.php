<?php

namespace Tests\Unit;

use KBox\User;
use KBox\Flags;
use KBox\Consent;
use KBox\Consents;
use KBox\HomeRoute;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Support\Facades\Storage;

class GetHomeRouteTest extends TestCase
{
    public function expected_route_for_capabilities()
    {
        return [
            [Capability::$ADMIN, 'documents.index'],
            [Capability::$UPLOADER, 'documents.index'],
            [Capability::$PROJECT_MANAGER, 'documents.index'],
            [Capability::$PROJECT_MANAGER_LIMITED, 'documents.index'],
            [Capability::$PARTNER, 'documents.index'],
            [[Capability::RECEIVE_AND_SEE_SHARE], 'shares.index'],
            [[], 'search'],
        ];
    }
    
    public function expected_route_for_capabilities_based_on_flags()
    {
        return [
            [Capability::$ADMIN, Flags::HOME_ROUTE_PROJECTS, 'documents.projects.index'],
            [Capability::$UPLOADER, Flags::HOME_ROUTE_PROJECTS, 'documents.projects.index'],
            [Capability::$PROJECT_MANAGER, Flags::HOME_ROUTE_PROJECTS, 'documents.projects.index'],
            [Capability::$PROJECT_MANAGER_LIMITED, Flags::HOME_ROUTE_PROJECTS, 'documents.projects.index'],
            [Capability::$PARTNER, Flags::HOME_ROUTE_PROJECTS, 'documents.projects.index'],
            [[Capability::RECEIVE_AND_SEE_SHARE], Flags::HOME_ROUTE_PROJECTS, 'shares.index'],
            [[], Flags::HOME_ROUTE_PROJECTS, 'search'],
            [Capability::$ADMIN, Flags::HOME_ROUTE_RECENT, 'documents.recent'],
            [Capability::$UPLOADER, Flags::HOME_ROUTE_RECENT, 'documents.recent'],
            [Capability::$PROJECT_MANAGER, Flags::HOME_ROUTE_RECENT, 'documents.recent'],
            [Capability::$PROJECT_MANAGER_LIMITED, Flags::HOME_ROUTE_RECENT, 'documents.recent'],
            [Capability::$PARTNER, Flags::HOME_ROUTE_RECENT, 'documents.recent'],
            [[Capability::RECEIVE_AND_SEE_SHARE], Flags::HOME_ROUTE_RECENT, 'shares.index'],
            [[], Flags::HOME_ROUTE_RECENT, 'search'],
        ];
    }
    
    /**
     * @dataProvider expected_route_for_capabilities
     */
    public function test_user_home_route_is_returned($capabilities, $route)
    {
        Storage::fake('app');

        $user = tap(factory(User::class)->create(['name' => 'canary']), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
        });

        Consent::agree($user, Consents::PRIVACY);

        $real = HomeRoute::get($user);
        $expected = route($route);

        $this->assertEquals($expected, $real);
    }
    
    /**
     * @dataProvider expected_route_for_capabilities
     */
    public function test_user_home_route_is_returned_if_consent_not_given_and_page_dont_exists($capabilities, $route)
    {
        Storage::fake('app');
        
        $user = tap(factory(User::class)->create(['name' => 'canary']), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
        });

        Consent::withdraw($user, Consents::PRIVACY);

        $real = HomeRoute::get($user);
        $expected = route($route);

        $this->assertEquals($expected, $real);
    }
    
    /**
     * @dataProvider expected_route_for_capabilities_based_on_flags
     */
    public function test_user_home_route_respect_flags($capabilities, $flag, $route)
    {
        $user = tap(factory(User::class)->create(['name' => 'canary']), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
        });

        Consent::agree($user, Consents::PRIVACY);

        Flags::enable($flag);

        $real = HomeRoute::get($user);
        $expected = route($route);

        $this->assertEquals($expected, $real);
    }
}
