<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Institution;
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
    
    public function user_provider_document_link_test() {
        return array( 
			array(Capability::$ADMIN, true),
			array(Capability::$PROJECT_MANAGER, true),
			array(Capability::$PARTNER, false),
		);
    }
    
    public function user_provider_document_link_login_with_second_user_test() {
        return array( 
			array(Capability::$ADMIN, Capability::$ADMIN),
			array(Capability::$ADMIN, Capability::$PROJECT_MANAGER),
			array(Capability::$ADMIN, Capability::$PARTNER),
			array(Capability::$PROJECT_MANAGER, Capability::$ADMIN),
			array(Capability::$PROJECT_MANAGER, Capability::$PROJECT_MANAGER),
			array(Capability::$PROJECT_MANAGER, Capability::$PARTNER),
			array(Capability::$PARTNER, Capability::$PARTNER),
			array(Capability::$PARTNER, Capability::$ADMIN),
			array(Capability::$PARTNER, Capability::$PROJECT_MANAGER),
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
    
    /**
     * Tests if when using a document link the login page is firstly showed and then performs 
     * the redirect to the document page correctly
     *
     * @dataProvider user_provider_document_link_test
     */
    public function testDocumentLinkLoginRedirect_NotLoggedIn($cap, $ignored){
        
        // create a document
        
        $user_password = str_random(10);
        
        $user = $this->createUser( $cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ] );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id
        ]);
        
        $doc->document_uri = route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        
        $doc->save();
        
        // get the link
        $doc_link = \DmsRouting::preview($doc);
        
        // goto link, see login page
        $this->visit( $doc_link )->seePageIs( route('auth.login') );
        
        // perform login
        $this->type($user->email, 'email');
        $this->type($user_password, 'password');
        
        $this->press(trans('login.form.submit'));
        
        
        // see document page
        
        // var_dump($this->response->getContent());
        
        $this->seePageIs( $doc_link );
        
    }
    
    /**
     * Tests the document link for an already logged-in user 
     *
     * @dataProvider user_provider_document_link_test
     */
    public function testDocumentLinkLoginRedirect_LoggedIn($cap, $ignored){
        
        // create a document
        
        $user_password = str_random(10);
        
        $user = $this->createUser( $cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ] );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id
        ]);
        
        $doc->document_uri = route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        
        $doc->save();
        
        // get the link
        $doc_link = \DmsRouting::preview($doc);
        
        $this->actingAs($user);
        
        // goto link, see linked page
        $this->visit( $doc_link )->seePageIs( $doc_link );
        
    }
    
    /**
     * Tests if when using a document link, after the login process has been done, works as expected 
     *
     * @dataProvider user_provider_document_link_test
     */
    public function testDocumentLinkLoginRedirect_LoginThen($cap, $ignored){
        
        // create a document
        
        $user_password = str_random(10);
        
        $user = $this->createUser( $cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ] );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id
        ]);
        
        $doc->document_uri = route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        
        $doc->save();
        
        // get the link
        $doc_link = \DmsRouting::preview($doc);
        
        // goto link, see login page
        $this->visit( route('auth.login') )->seePageIs( route('auth.login') );
        
        // perform login
        $this->type($user->email, 'email');
        $this->type($user_password, 'password');
        
        $this->press(trans('login.form.submit'));
        
        // see document page
        
        $this->visit( $doc_link )->seePageIs( $doc_link );
        
    }
    
    /**
     * Tests if when using a document link, after the login process has been done, works as expected 
     *
     * @dataProvider user_provider_document_link_login_with_second_user_test
     */
    public function testDocumentLinkLoginRedirect_LoginWithSecondUser($cap, $second_user_cap){
        
        // create a document
        
        $user_password = str_random(10);
        
        $owner = $this->createUser( $cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ] );
        
        $user = $this->createUser( $second_user_cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ] );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $owner->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $owner->id,
            'file_id' => $file->id
        ]);
        
        $doc->document_uri = route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        
        $doc->save();
        
        // get the link
        $doc_link = \DmsRouting::preview($doc);
        
        // goto link, see login page
        $this->visit( $doc_link )->seePageIs( route('auth.login') );
        
        // perform login
        $this->type($user->email, 'email');
        $this->type($user_password, 'password');
        
        $this->press(trans('login.form.submit'));
        
        
        // see document page
        
        // var_dump($this->response->getContent());
        
        $this->seePageIs( $doc_link );
        
    }
    
}