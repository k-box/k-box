<?php

use KBox\User;
use KBox\Capability;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Basic tests of GET routes that requires the user to be authenticated or not
*/
class RoutesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function routes_provider()
    {
        return [
            [ 'frontpage' ],
            [ 'contact' ],
            [ 'help' ],
            [ 'browserupdate' ],
            [ 'login' ],
        ];
    }
    
    public function protected_routes_provider()
    {
        return [
            [ 'administration.index' ],
            [ 'administration.institutions.index' ],
            [ 'administration.mail.index' ],
            [ 'administration.maintenance.index' ],
            [ 'administration.messages.create' ],
            [ 'administration.network.index' ],
            [ 'administration.storage.index' ],
            [ 'administration.storage.files' ],
            [ 'administration.users.index' ],
            [ 'administration.users.create' ],
            [ 'documents.index' ],
            [ 'documents.create' ],
            [ 'documents.groups.create' ],
            [ 'documents.recent' ],
            [ 'documents.sharedwithme' ],
            [ 'documents.starred.index' ],
            [ 'documents.trash' ],
            [ 'profile.index' ],
            [ 'projects.index' ],
            [ 'projects.create' ],
            [ 'shares.index' ],
            [ 'shares.create' ],
        ];
    }
    
    public function routes_with_login_provider()
    {
        return [
            [Capability::$ADMIN, 200],
            [[Capability::MANAGE_KBOX], 403],
            [Capability::$PROJECT_MANAGER, 200],
            [Capability::$PARTNER, 200],
            [[Capability::RECEIVE_AND_SEE_SHARE], 403],
        ];
    }

    /**
     * Tests routes that do not need a login
     *
     * @dataProvider routes_provider
     * @return void
     */
    public function testFreeRoutes($name)
    {
        $this->withKlinkAdapterFake();
        
        $url = route($name);
        
        $this->visit($url)->seePageIs($url);
        
        $this->assertResponseOk();
    }
    
    /**
     * Tests routes that need a login and hence are expecting to redirect the user to the login page
     *
     * @dataProvider protected_routes_provider
     * @return void
     */
    public function testRedirectToLogin($name)
    {
        $url = route($name);
        
        $this->visit($url)->seePageIs(route('frontpage'));
        
        $this->assertResponseOk();
    }
}
