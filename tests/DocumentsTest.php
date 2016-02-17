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
class DocumentsTest extends TestCase {
    
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
	public function testDocumentShowPage( )
	{
        // create a document
        
        $user = $this->createAdminUser();
        
        $user_not_owner = $this->createAdminUser();
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
        ]);
        
        // test without login
        
        $url = route( 'documents.show', $doc->id );
        
        $this->visit( $url )->seePageIs( route('auth.login') );

        
        // test with login with the owner user
		
        $this->actingAs($user);
        
        $this->visit( $url )->seePageIs( $url );
        
        $this->assertResponseOk();
        
        // test with login with another user
        
        $this->actingAs($user_not_owner);
        
        $this->visit( $url )->see( trans('errors.403_title') );
        
	}
    
    /**
	 * Tests if the edit page shows the "make public" checkbox only if the user can do the operation 
	 *
     * @dataProvider user_provider_for_editpage_public_checkbox_test
	 * @return void
	 */
    public function testDocumentEditPageForKlinkPublicCheckBox( $caps, $can_see )
	{
        
        $user = $this->createUser( $caps );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
        ]);
        
        
        $url = route( 'documents.edit', $doc->id );
        
        $this->actingAs($user);
        
        $this->visit( $url );
        
        $this->assertResponseOk();
        
        if( $can_see ){
            $this->see('Make Public');
        }
        else {
            $this->dontSee('Make Public');
        }
  		
        
        
        
        
		
	}
    
}