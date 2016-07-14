<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Institution;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Support\Facades\Artisan;
use KlinkDMS\Exceptions\ForbiddenException;


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
    
    public function vibility_provider() {
        return array( 
			array('public'),
			array('private'),
        );
    }

    public function user_provider_that_can_make_public() {
        return array( 
			array(Capability::$ADMIN),
			array(Capability::$PROJECT_MANAGER),
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
	 * Tests if the edit page is showed even if the user uploader of the document was disabled 
	 *
     * @dataProvider user_provider_for_editpage_public_checkbox_test
	 * @return void
	 */
    public function testDocumentEditPageWhenUserDisabled( $caps, $can_see )
	{
        
        $user = $this->createUser( $caps );
        $user2 = $this->createUser( $caps );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
        ]);

        $user->delete();

        $url = route( 'documents.edit', $doc->id );
        
        $this->actingAs($user2);
        
        $this->visit( $url );
        
        $this->assertResponseOk();
  		
	}
    
    /**
	 * Tests if the update for submit from the edit page is done correctly 
	 *
     * parameter $ignored is not taken into consideration due to reuse of an existing dataProvider
     *
     * @dataProvider user_provider_document_link_test
	 * @return void
	 */
    public function testDocumentUpdate( $caps, $ignored )
	{
        
        $user = $this->createUser( $caps );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id
        ]);
        
        
        $url = route( 'documents.edit', $doc->id );
        
        $this->actingAs($user);
        
        $this->visit( $url );
        
        $this->type('Document new Title', 'title');
        
        $this->press(trans('actions.save'));
        
        $this->seePageIs( $url );
        
        $this->see(trans('documents.messages.updated'));
        
        $this->see('Document new Title');
        
  		
	}
    
    /**
     * @dataProvider user_provider_that_can_make_public
     */
    public function testDocumentUpdateMakePublicFromEditPage($caps){
        
        $user = $this->createUser( $caps );

        $doc = $this->createDocument($user);
        
        // $file = factory('KlinkDMS\File')->create([
        //     'user_id' => $user->id,
        //     'original_uri' => ''
        // ]);
        
        // $doc = factory('KlinkDMS\DocumentDescriptor')->create([
        //     'owner_id' => $user->id,
        //     'file_id' => $file->id
        // ]);
        
        $url = route( 'documents.edit', $doc->id );
        
        $this->actingAs($user);
        
        $this->visit( $url );
        
        // Make public
        
        $this->check('visibility');
        
        $this->press(trans('actions.save'));
        
        $this->seePageIs( $url );
        
        $saved = DocumentDescriptor::findOrFail($doc->id);
        
        $this->assertTrue($saved->is_public);
        
        // Make private again
        
        $this->uncheck('visibility');
        
        $this->press(trans('actions.save'));
        
        $this->seePageIs( $url );
        
        $saved = DocumentDescriptor::findOrFail($doc->id);
        
        $this->assertFalse($saved->is_public);
        
    }
    
    
    /**
     * @dataProvider user_provider_that_can_make_public
     */
    public function testDocumentUpdateRemoveCollectionFromPublicDocument($caps){
        $user = $this->createUser( $caps );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'is_public' => true
        ]);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $group = $service->createGroup($user, 'Personal collection of user ' . $user->id);
        
        $group->documents()->save($doc);
        
        
        $this->actingAs($user);
		
        \Session::start(); // Start a session for the current test

		$this->json( 'PUT', route('documents.update', ['id' => $doc->id]), [
                 '_token' => csrf_token(),
                'remove_group' => $group->id]);
        
        $this->seeJson([
            'id' => $doc->id,
            'is_public' => true,
        ]);
        
        $this->assertResponseStatus(200);
        
    }
    
    /**
     * @dataProvider user_provider_that_can_make_public
     */
    public function testDocumentUpdateAddCollectionToPublicDocument($caps){
        $user = $this->createUser( $caps );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'is_public' => true
        ]);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $group = $service->createGroup($user, 'Personal collection of user ' . $user->id);
        
        $this->actingAs($user);
		
        \Session::start(); // Start a session for the current test

		$this->json( 'PUT', route('documents.update', ['id' => $doc->id]), [
                 '_token' => csrf_token(),
                'add_group' => $group->id]);
        
        $this->seeJson([
            'id' => $doc->id,
            'is_public' => true,
        ]);
        
        $this->assertResponseStatus(200);
        
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
    
    /**
     * Test the conversion from KlinkDMS\DocumentDescriptor to \KlinkDocumentDescriptor
     * @dataProvider vibility_provider
     */
    public function testDocumentDescriptorToKlinkDocumentDescriptor($visibility){
        
        $institution = factory('KlinkDMS\Institution')->create(); 
        
        $user = $this->createUser( Capability::$PROJECT_MANAGER, [
            'institution_id' => $institution->id
        ] );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'institution_id' => $institution->id,
            'is_public' => $visibility === 'private' ? false : true
        ]);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $group = $service->createGroup($user, 'Personal collection of user ' . $user->id);
        
        $group->documents()->save($doc);
        
        
        $descriptor = $doc->toKlinkDocumentDescriptor( $visibility === 'private' ? false : true );
        
        $this->assertNotNull($descriptor);
        
        $this->assertEquals($institution->klink_id, $descriptor->institutionID);
        
        $this->assertEquals($visibility, $descriptor->visibility);
        
        $this->assertEquals($doc->title, $descriptor->title);
        
        $this->assertEquals($doc->hash, $descriptor->hash, 'Descriptor hash not equal to DocumentDescriptor');
        $this->assertEquals($doc->hash, $file->hash, 'File Hash not equal to DocumentDescriptor hash');
        
        if($visibility === 'private'){
            $this->assertNotEmpty($descriptor->documentGroups);
            $this->assertEquals($group->toKlinkGroup(), $descriptor->documentGroups[0]);
        }
        else {
            $this->assertEmpty($descriptor->documentGroups);
        }
        
    } 
    
    /**
     * Test the conversion from KlinkDMS\DocumentDescriptor to \KlinkDocumentDescriptor
     * @dataProvider vibility_provider
     */
    public function testDocumentDescriptorToKlinkDocumentDescriptorWhenUserChangeAffiliation($visibility){
        
        $institution = factory('KlinkDMS\Institution')->create(); 
        $institution2 = factory('KlinkDMS\Institution')->create(); 
        
        $user = $this->createUser( Capability::$PROJECT_MANAGER, [
            'institution_id' => $institution->id
        ] );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'institution_id' => $institution->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'is_public' => $visibility === 'private' ? false : true
        ]);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $group = $service->createGroup($user, 'Personal collection of user ' . $user->id);
        
        $group->documents()->save($doc);
        
        $user->institution_id = $institution2->id;
        $user->save();
        
        $descriptor = $doc->toKlinkDocumentDescriptor( $visibility === 'private' ? false : true );
        
        $this->assertNotNull($descriptor);
        
        $this->assertNotEquals($user->institution->klink_id, $descriptor->institutionID);
        $this->assertEquals($institution->klink_id, $descriptor->institutionID);
        
        $this->assertEquals($visibility, $descriptor->visibility);
        
        $this->assertEquals($doc->title, $descriptor->title);
        
        $this->assertEquals($doc->hash, $descriptor->hash, 'Descriptor hash not equal to DocumentDescriptor');
        $this->assertEquals($doc->hash, $file->hash, 'File Hash not equal to DocumentDescriptor hash');
        
        if($visibility === 'private'){
            $this->assertNotEmpty($descriptor->documentGroups);
            $this->assertEquals($group->toKlinkGroup(), $descriptor->documentGroups[0]);
        }
        else {
            $this->assertEmpty($descriptor->documentGroups);
        }
        
    }
    
    
    public function testDocumentReindexingStartedByAUserThatIsNotTheOwner(){
        
        $institution = factory('KlinkDMS\Institution')->create(); 
        $institution2 = factory('KlinkDMS\Institution')->create(); 
        
        $user = $this->createUser( Capability::$PROJECT_MANAGER, [
            'institution_id' => $institution->id
        ] );
                
        
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'is_public' => false,
            'institution_id' => $institution->id
        ]);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $core = app('Klink\DmsAdapter\KlinkAdapter')->getConnection();
        
        // first indexing, like the one after the upload
        $service->reindexDocument($doc, 'private');
        
        $facets = \KlinkFacetsBuilder::i()->localDocumentID($doc->local_document_id)->build();
        
        // Search for it, must only be indexed once
        
        $search_results = $core->search('*', 'private', 10, 0, $facets);
        
        $this->assertEquals(1, $search_results->getTotalResults());
        
        
        // Pick another user
        
        $second_user = $this->createUser( Capability::$PARTNER, [
            'institution_id' => $institution2->id
        ] );
        
        // make an edit to the document, save it and then reindex
        
        $url = route( 'documents.edit', $doc->id );
        
        $this->actingAs($second_user);
        
        $this->visit( $url );
        
        $this->type('Document new Title', 'title');
        
        $this->press(trans('actions.save'));
        
        $this->seePageIs( $url );
        
        $this->see(trans('documents.messages.updated'));
        
        $this->see('Document new Title');
        
        $doc = DocumentDescriptor::findOrFail($doc->id);
        
        $this->assertEquals($institution->id, $doc->institution_id);
        
        $search_results = $core->search('*', 'private', 10, 0, $facets);
        
        $this->assertEquals(1, $search_results->getTotalResults(), 'not only one result');
        
        
    }
    
    public function testDocumentStoreForInstitution(){
        
        $institution = factory('KlinkDMS\Institution')->create();
        
        $user = $this->createUser( Capability::$PARTNER, [
            'institution_id' => $institution->id
        ] );
        
        $url = route( 'documents.create' );
        
        $this->actingAs($user);
        
        $this->visit( $url );
        
        $this->attach(__DIR__ . '/data/example.pdf', 'document');
        
        $this->press(trans('actions.upload_alt'));
        
        preg_match('/action="(.*)\/(\d{1,4})" e/', $this->response->getContent(), $matches);
        
        $this->assertEquals(3, count($matches));
        
        $id = $matches[2];
        
        $doc = DocumentDescriptor::findOrFail($id);
        
        $this->assertEquals($user->id, $doc->owner_id, 'User not equal to owner');
        $this->assertEquals($user->institution_id, $doc->institution_id, 'User Institution not equal to document institution_id');

    }


    public function testDocumentService_deleteDocument(){

        $user = $this->createUser( Capability::$PARTNER );

        $doc = $this->createDocument($user);

        $service = app('Klink\DmsDocuments\DocumentsService');

        $is_deleted = $service->deleteDocument($user, $doc);

        $this->assertTrue($is_deleted);

        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);

        $this->assertTrue($doc->trashed());

        $this->assertNull($doc->file);

        $file = File::withTrashed()->findOrFail($doc->file_id);

        $this->assertTrue($file->trashed());

    }

    /**
     * @expectedException KlinkDMS\Exceptions\ForbiddenException
     */
    public function testDocumentService_deleteDocument_forbidden(){

        $user = $this->createUser( Capability::$GUEST );

        $doc = $this->createDocument($user);

        $service = app('Klink\DmsDocuments\DocumentsService');

        $service->deleteDocument($user, $doc);

    }

    public function testDocumentDelete(){

        $user = $this->createUser( Capability::$PARTNER);

        $doc = $this->createDocument($user);

        \Session::start();

        $url = route( 'documents.destroy', ['id' => $doc->id, 
                '_token' => csrf_token()] );
        
        $this->actingAs($user);

        $this->delete( $url );

        $this->see('ok');
		
		$this->assertResponseStatus(202);

        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);

        $this->assertTrue($doc->trashed());

    }

    
    
    public function testDocumentService_permanentlyDeleteDocument(){

        $user = $this->createUser( Capability::$PROJECT_MANAGER );

        $doc = $this->createDocument($user);

        $service = app('Klink\DmsDocuments\DocumentsService');

        $service->deleteDocument($user, $doc); // put doc in trash
        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);


        $is_deleted = $service->permanentlyDeleteDocument($user, $doc);
        
        $this->assertTrue($is_deleted);

        $exists_doc = DocumentDescriptor::withTrashed()->exists($doc->id);

        $this->assertFalse($exists_doc);

        $file = File::withTrashed()->find($doc->file_id);

        $this->assertNull($file);
        
    }

    /**
     * @expectedException KlinkDMS\Exceptions\ForbiddenException
     */
    public function testDocumentService_permanentlyDeleteDocument_forbidden(){

        $user = $this->createUser( Capability::$PARTNER );

        $doc = $this->createDocument($user);

        $service = app('Klink\DmsDocuments\DocumentsService');

        $service->deleteDocument($user, $doc); // put doc in trash

        $is_deleted = $service->permanentlyDeleteDocument($user, $doc);
        
    }


    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testDocumentForceDelete(){

        $user = $this->createUser( Capability::$PROJECT_MANAGER);

        $doc = $this->createDocument($user);

        \Session::start();

        $url = route( 'documents.destroy', ['id' => $doc->id, 
                '_token' => csrf_token()] );
        
        $this->actingAs($user);

        $this->delete( $url );

        $this->see('ok');
		
		$this->assertResponseStatus(202);

        $url = route( 'documents.destroy', [
                'id' => $doc->id, 
                'force' => true, 
                '_token' => csrf_token()] );

        $this->delete( $url );

        $this->see('ok');
		
		$this->assertResponseStatus(202);

        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);


    }

    public function testDocumentReferenceDelete(){
        $this->markTestIncomplete(
          'Implementation needed.'
        );
    }
    
}