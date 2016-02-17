<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Support\Facades\Artisan;


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test something related to document descriptors management
*/
class CollectionsTest extends TestCase {
    
    use DatabaseTransactions;
    
    
    public function user_provider_for_editpage_public_checkbox_test() {
        return array( 
			array(Capability::$ADMIN, true),
			array(Capability::$DMS_MASTER, false),
			array(Capability::$PROJECT_MANAGER, true),
			array(Capability::$PARTNER, false),
			array(Capability::$GUEST, false),
		);
    }

	/**
	 * Test that the route for documents.show is not leaking private documents to anyone
	 *
	 * @return void
	 */
	public function testSeePersonalCollectionLoginRequired( )
	{
        // create a document
        
        $user = $this->createAdminUser();
        
        // $user_not_owner = $this->createAdminUser();
        
        $service = app('Klink\DmsDocuments\DocumentsService');        
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route( 'documents.groups.show', $collection->id );
        
        $this->visit( $url )->seePageIs( route('auth.login') );
        
	}
    
    public function testSeePersonalCollectionAccessGranted( )
	{
        // create a document
        
        $user = $this->createAdminUser();
        
        $service = app('Klink\DmsDocuments\DocumentsService');        
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route( 'documents.groups.show', $collection->id );
        
        // test with login with the owner user
		
        $this->actingAs($user);
        
        $this->visit( $url )->seePageIs( $url );
        
        $this->assertResponseOk();
        
        
	}
    
    public function testSeePersonalCollectionAccessDenieded( )
	{
        // create a document
        
        $user = $this->createAdminUser();
        
        $user_not_owner = $this->createAdminUser();
        
        $service = app('Klink\DmsDocuments\DocumentsService');        
        
        $collection = $service->createGroup($user, 'Personal Collection Name');

        $url = route( 'documents.groups.show', $collection->id );
        
        // test with login with another user
        
        $this->actingAs($user_not_owner);
        
        $this->call( 'GET', $url );
        
        $this->assertResponseStatus(403);
        
	}
       
}