<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Capability;
use Illuminate\Support\Facades\Artisan;


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Basic tests of GET routes that requires the user to be authenticated or not
*/
class RoutesTest extends TestCase {
    
	use DatabaseTransactions;
    
    public function routes_provider(){
		
		return array( 
			array( 'frontpage' ),
			array( 'auth.login' ),
			array( 'password.reset' ),
			array( 'contact' ),
			array( 'privacy' ),
			array( 'terms' ),
			array( 'help' ),
			array( 'browserupdate' ),
		);
        
	}
    
    public function protected_routes_provider(){
		
		return array( 
			array( 'administration.index' ),
			array( 'administration.institutions.index' ),
			array( 'administration.institutions.create' ),
			array( 'administration.mail.index' ),
			array( 'administration.maintenance.index' ),
			array( 'administration.messages.create' ),
			array( 'administration.network.index' ),
			array( 'administration.storage.index' ),
			array( 'administration.users.index' ),
			array( 'administration.users.create' ),
			array( 'documents.index' ),
			array( 'documents.create' ),
			array( 'documents.groups.create' ),
			array( 'documents.groups.index' ),
			array( 'import' ),
			array( 'documents.notindexed' ),
			array( 'documents.recent' ),
			array( 'documents.sharedwithme' ),
			array( 'documents.starred.index' ),
			array( 'documents.trash' ),
			array( 'people.index' ),
			array( 'profile.index' ),
			array( 'projects.index' ),
			array( 'projects.create' ),
			array( 'shares.index' ),
			array( 'shares.create' ),
		);
        
	}
    
    
    public function routes_with_login_provider() {
        return array( 
			array(Capability::$ADMIN, 200),
			array(Capability::$DMS_MASTER, 403),
			array(Capability::$PROJECT_MANAGER, 200),
			array(Capability::$PARTNER, 200),
			array(Capability::$GUEST, 403),
		);
    }

	/**
	 * Tests routes that do not need a login
	 *
     * @dataProvider routes_provider
	 * @return void
	 */
	public function testFreeRoutes( $name )
	{

		$this->withKlinkAdapterFake();
        
        $url = route( $name );
        
        $this->visit( $url )->seePageIs( $url );
  		
        $this->assertResponseOk();
		
	}
    
    /**
	 * Tests routes that need a login and hence are expecting to redirect the user to the login page
	 *
     * @dataProvider protected_routes_provider
	 * @return void
	 */
	public function testRedirectToLogin( $name )
	{
        
        $url = route( $name );
        
        $this->visit( $url )->seePageIs( route('frontpage') );
  		
        $this->assertResponseOk();
		
	}
 
    // /**
	//  * Tests routes that need a login
	//  *
    //  * @dataProvider routes_with_login_provider
	//  * @return void
	//  */
	// public function testProtectedRoutes($caps, $expected_code)
	// {
    //     
    // }   
    
	
}