<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Institution;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Project;
use Illuminate\Support\Facades\Artisan;
use KlinkDMS\Exceptions\ForbiddenException;
use Carbon\Carbon;
use KlinkDMS\Flags;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;

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


    public function data_provider_for_facets_composer() {
        return array( 
            array([], ''),
			array(['s' => 'pasture'], '?s=pasture'),
		);
    }

    public function recent_date_range_provider() {
        return array( 
			array('today'),
			array('yesterday'),
			array('currentweek'),
			array('currentmonth'),
        );
    }

    public function recent_items_per_page_provider() {
        return array( 
			array(5),
			array(10),
			array(15),
			array(25),
			array(50),
        );
    }

    public function recent_sorting_provider() {
        return array( 
			array('a', 'ASC'),
			array('d', 'DESC'),
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
            $this->see( trans('networks.publish_to_long', ['network' => network_name()]) );
        }
        else {
            $this->dontSee(trans('networks.publish_to_long', ['network' => network_name()]));
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

        $this->withKlinkAdapterFake();
        
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

        $this->withKlinkAdapterFake();
        
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

        $this->withKlinkAdapterFake();

        $user = $this->createUser( $caps );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'is_public' => true,
            'hash' => $file->hash
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

        $this->withKlinkAdapterFake();

        $user = $this->createUser( $caps );
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'is_public' => true,
            'hash' => $file->hash
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

        $this->withKlinkAdapterFake();
        
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

        $this->withKlinkAdapterFake();
        
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

        $this->withKlinkAdapterFake();
        
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

        $this->withKlinkAdapterFake();
        
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

        $this->withKlinkAdapterFake();
        
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

        $this->withKlinkAdapterFake();
        
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

        $fake = $this->withKlinkAdapterFake();
        
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
        
        // first indexing, like the one after the upload
        $service->reindexDocument($doc, 'private');
        
        $facets = \KlinkFacetsBuilder::i()->localDocumentID($doc->local_document_id)->build();
        
        // Search for it, must only be indexed once
        
        $fake->assertDocumentIndexed($doc->local_document_id);
        
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
        
        $fake->assertDocumentIndexed($doc->local_document_id, 2);
        
    }
    
    public function testDocumentStoreForInstitution(){
        
        $this->markTestIncomplete('Test why is not going to the documents.edit page after upload');
        
        $institution = factory('KlinkDMS\Institution')->create();
        
        $user = $this->createUser( Capability::$PARTNER, [
            'institution_id' => $institution->id
        ] );
        
        $url = route( 'documents.create' );
        
        $this->actingAs($user);
        
        $this->visit( $url );
        
        $this->attach(__DIR__ . '/data/example.pdf', 'document');
        
        $this->press(trans('actions.upload_alt'));


        // $this->assertViewHas('document');

        // var_dump($this->response->getContent());
        // preg_match('/action="(.*)\/(\d{1,4})" e/', $this->response->getContent(), $matches);
        // var_dump($matches);
        // $this->assertEquals(3, count($matches));
        
        // $id = $matches[2];
        
        // $doc = DocumentDescriptor::findOrFail($id);
        
        // $this->assertEquals($user->id, $doc->owner_id, 'User not equal to owner');
        // $this->assertEquals($user->institution_id, $doc->institution_id, 'User Institution not equal to document institution_id');

    }


    public function testDocumentService_deleteDocument(){

        $this->withKlinkAdapterFake();

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

        $this->withKlinkAdapterFake();

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

        $this->withKlinkAdapterFake();

        $user = $this->createUser( Capability::$PROJECT_MANAGER );

        $doc = $this->createDocument($user);

        $service = app('Klink\DmsDocuments\DocumentsService');

        $service->deleteDocument($user, $doc); // put doc in trash
        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);


        $is_deleted = $service->permanentlyDeleteDocument($user, $doc);
        $this->assertTrue($is_deleted);

        $exists_doc = DocumentDescriptor::withTrashed()->find($doc->id);

        $this->assertNull($exists_doc, 'Document still exists');

        $file = File::withTrashed()->find($doc->file_id);

        $this->assertNull($file);
        
    }

    /**
     * @expectedException KlinkDMS\Exceptions\ForbiddenException
     */
    public function testDocumentService_permanentlyDeleteDocument_forbidden(){

        $this->withKlinkAdapterFake();

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

        $this->withKlinkAdapterFake();

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


    public function testNewDocumentVersionUpload(){

        $this->withKlinkAdapterFake();

        // upload a document (faked by already existing entry in the database)

        $user = $this->createUser( Capability::$PROJECT_MANAGER);

        $doc = $this->createDocument($user);

        // create a new version, with different mime_type

        $new_path = base_path('tests/data/example-presentation.pptx');
        $new_mime_type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        $new_document_type = 'presentation';
        $new_hash = \KlinkDocumentUtils::generateDocumentHash( base_path('tests/data/example-presentation.pptx') );

        $this->actingAs($user);
        
        $this->visit( route('documents.edit', $doc->id ))
          ->see( trans('documents.versions.new_version_button') )
          ->attach(base_path('tests/data/example-presentation.pptx'), 'document')
          ->press( trans('actions.save') /*trans('documents.versions.new_version_button')*/)
          ->see( trans('documents.messages.updated') )
          ->dontSee( 'documents.messages.updated' );

        // check change to: mime_type, document_type, hash and file_id. No changes should be applied to local_document_id

        $updated_descriptor = $doc->fresh();


        $this->assertEquals($updated_descriptor->mime_type, $new_mime_type, 'Mime type not matching');
        $this->assertEquals($updated_descriptor->document_type, $new_document_type, 'Document type not matching');
        $this->assertEquals($updated_descriptor->hash, $new_hash, 'Hash not matching');
        $this->assertEquals($updated_descriptor->local_document_id, $doc->local_document_id, 'Local document ID not matching');
    }

    public function testDocumentReferenceDelete(){
        $this->markTestIncomplete(
          'Implementation needed.'
        );
    }


    public function testDocumentDescriptorFileRelation(){

        $user = $this->createAdminUser();

        $descr = $this->createDocument($user);

        $file = $descr->file;

        $this->assertNotNull($file);
        $this->assertEquals($descr->hash, $file->hash);

        
        $this->assertNotNull($file->document);

        $relatedDocument = $file->document;

        $this->assertEquals($descr->id, $relatedDocument->id);
        $this->assertEquals($descr->hash, $relatedDocument->hash);

        

    }

    public function testFileRevisions(){

        $user = $this->createAdminUser();

        $descr = $this->createDocument($user);
        
        $file = $descr->file;

        $revision_of_revision = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $file->path,
            'revision_of' => null,
        ]);

        $revision = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $file->path,
            'revision_of' => $revision_of_revision->id,
        ]);


        $file->revision_of = $revision->id;

        $file->save();

        $this->assertEquals(3, $file->getVersions()->count()); // 2 old revisions + the current version
        $this->assertEquals(1, $revision_of_revision->getVersions()->count()); // only the current version

        $lastVersion = $revision_of_revision->getLastVersion();

        $this->assertEquals($file->id, $lastVersion->id);

    }

    /**
     * test that title attribute on collections filter is reporting the parents 
     * of a collection. Test also that the correct classes, based on group being a project
     * are applied to the filter element
     */
    public function testParentCollectionsOnFilters(){

        $this->markTestIncomplete('Change the test implementation to use the Mock instead of the Fake KlinkAdapter');

        $this->withKlinkAdapterFake();

        $project_collection_names = ['Project Root', 'Project First Level', 'Second Level'];

        $user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $project = null;
        $project_group = null;
        $project_group_root = null;
        $project_childs = count($project_collection_names);

        foreach ($project_collection_names as $index => $name) {
            
            $project_group = $service->createGroup($user, $name, null, $project_group, false);

            if($index === 0){
                $project = Project::create([
                    'name' => $name,
                    'user_id' => $user->id,
                    'collection_id' => $project_group->id,
                ]);
                $project_group_root = $project_group;
            }
            
        }

        // add and index a document in "C", both project and personal

        $descriptor = $this->createDocument($user);

        $service->addDocumentToGroup($user, $descriptor, $project_group);
        $descriptor = $descriptor->fresh();

        // goto private documents page

        $this->actingAs($user);
        
        // goto link, see linked page

        $url = route( 'documents.groups.show', ['id' => $project_group->id] );

        $this->visit( $url )->seePageIs( $url );

        // test what's inside the view data for filters/facets

        $this->see( trans('actions.filters.filter') );

        // The next 4 lines are an hack to get the view data enhanced by the composer
        // The final view will not have this data, because the facets.blade.php template
        // is internal to the page view, therefore already rendered when the response ends

        $view = $this->response->original; // is a view
        $composer = app('KlinkDMS\Http\Composers\DocumentsComposer');
        $composer->facets($view);
        $this->response->original = $view;

        // --- end hack

        $this->assertViewHas('columns');

        $filters = $view->columns['documentGroups'];
        
        $this->assertNotNull($filters);
        $this->assertNotEmpty($filters['items']);

        $item = collect($filters['items'])->first();

        $this->assertNotNull($item);
        
        $this->assertTrue($item->is_project);
        $this->assertEquals('Project Root > Project First Level', $item->parents, 'Parents order');

        // maybe confirm that class X is applied to .el-filter
        $this->see('project--mark');

        $this->see('Project Root > Project First Level');

    }

    /**
     * @dataProvider data_provider_for_facets_composer
     */
    public function testFacetsViewComposerUrlConstruction($search_parameters, $expected_url){

        $this->withKlinkAdapterFake();

        $project_collection_names = ['Project Root', 'Project First Level', 'Second Level'];

        $user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $project = null;
        $project_group = null;
        $project_group_root = null;
        $project_childs = count($project_collection_names);

        foreach ($project_collection_names as $index => $name) {
            
            $project_group = $service->createGroup($user, $name, null, $project_group, false);

            if($index === 0){
                $project = Project::create([
                    'name' => $name,
                    'user_id' => $user->id,
                    'collection_id' => $project_group->id,
                ]);
                $project_group_root = $project_group;
            }
            
        }

        // add and index a document in "C", both project and personal

        $descriptor = $this->createDocument($user);

        $service->addDocumentToGroup($user, $descriptor, $project_group);
        $descriptor = $descriptor->fresh();

        // goto private documents page

        $this->actingAs($user);
        
        // goto link, see linked page

        $url = route( 'documents.groups.show', array_merge(['id' => $project_group->id], $search_parameters) );

        $this->visit( $url )->seePageIs( $url );

        // The next 4 lines are an hack to get the view data enhanced by the composer
        // The final view will not have this data, because the facets.blade.php template
        // is internal to the page view, therefore already rendered when the response ends

        $view = $this->response->original; // is a view
        $composer = app('KlinkDMS\Http\Composers\DocumentsComposer');
        $composer->facets($view);
        $this->response->original = $view;

        // --- end hack

        $this->assertViewHas('facet_filters_url');
        $this->assertViewHas('current_active_filters');

        $this->assertViewHas('columns');

        $base_url = $view->facet_filters_url;
        
        $this->assertEquals($expected_url, $base_url);

        $this->assertViewHas('clear_filter_url');

        $this->assertEquals($url, $view->clear_filter_url);

    }

    /**
     * @dataProvider recent_date_range_provider 
     */
    public function testRecentPageRangeSelection($range){

        Flags::enable(Flags::UNIFIED_SEARCH);

        $this->withKlinkAdapterFake();

        $user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
        $this->actingAs($user);
        
        // goto link, see linked page

        $url = route( 'documents.recent', ['range' => $range] );

        $this->visit( $url )->seePageIs( $url );

        $this->assertViewHas('range', $range);

        $user = $user->fresh();
        $this->assertEquals($range, $user->optionRecentRange());

    }
    
    /**
     * Test Items per page option is honored
     *
     * @dataProvider recent_items_per_page_provider 
     */
    public function testRecentPageItemsPerPageSelection($items_per_page){

        Flags::enable(Flags::UNIFIED_SEARCH);

        $this->withKlinkAdapterFake();

        $user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
        $this->actingAs($user);

        for ($i=0; $i < $items_per_page; $i++) { 
            $this->createDocument($user);
        }

        $url = route( 'documents.recent') . '?n=' . $items_per_page ;

        $this->visit( $url )->seePageIs( $url );
        $user = $user->fresh();
        $this->assertEquals($items_per_page, $user->optionItemsPerPage());
        
        $this->assertEquals($items_per_page, 
            $this->response->original->documents->values()->collapse()->count() );

    }
    
    /**
     * Test sorting option is honored
     *
     * @dataProvider recent_sorting_provider 
     */
    public function testRecentPageSortingSelection($option, $expected_value){

        $this->withKlinkAdapterFake();

        $user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
        $this->actingAs($user);

        // for ($i=0; $i < $items_per_page; $i++) { 
        //     $this->createDocument($user);
        // }

        $url = route( 'documents.recent') . '?o=' . $option ;

        $this->visit( $url )->seePageIs( $url );
        
        $this->assertViewHas('order', $expected_value);

    }
    
    /**
     * Test recent page contains expected documents
     *
     * - last updated by user
     * - last shared with me
     */
    public function testRecentPageContainsExpectedDocuments($range = 'today'){

        $this->withKlinkAdapterFake();

        $user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
        $user2 = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
        
        $this->actingAs($user);

        $documents = collect(); // documents created by $user
        $documents_user2 = collect();  // documents created by $user2

        $count_documents_by_me = 5;
        $count_documents_shared_with_me = 1;
        $count_documents_in_project = 1;

        // create some documents for $user

        for ($i=0; $i < $count_documents_by_me; $i++) { 
            $documents->push( $this->createDocument($user) );
        }
        

        $doc = null;
        
        // create a project using $user2, add 1 document in the project
        $project1 = $this->createProject(['user_id' => $user2->id]);
        $project1->users()->attach($user->id);
        $doc = $this->createDocument($user2);
        $service = app('Klink\DmsDocuments\DocumentsService');
        $service->addDocumentToGroup($user2, $doc, $project1->collection);
        $doc = $doc->fresh();
        $documents_user2->push( $doc );

        // create a second user, share with the first one a couple of documents
        for ($i=0; $i < $count_documents_shared_with_me; $i++) { 
            $doc = $this->createDocument($user2);

            $doc->shares()->create(array(
						'user_id' => $user2->id,
						'sharedwith_id' => $user->id, //the id 
						'sharedwith_type' => get_class($user), //the class
						'token' => hash( 'sha512', $doc->id ),
					));
            $documents_user2->push( $doc );
        }

        // grab the last from $documents and change its updated_at to yesterday 
        // (wrt the selected $range)
        $last = $documents->last();

        $last->updated_at = Carbon::yesterday();

        $last->timestamps = false; //temporarly disable the automatic upgrade of the updated_at field

        $last->save();

        $url = route( 'documents.recent', ['range' => $range]);

        $this->visit( $url )->seePageIs( $url );

        $this->assertEquals(($count_documents_by_me - 1) + $count_documents_shared_with_me + $count_documents_in_project, 
            $this->response->original->documents->values()->collapse()->count() );
    }

    public function testRecentPageSearchWithFilters(){

        Flags::enable(Flags::UNIFIED_SEARCH);

        $docs = factory('KlinkDMS\DocumentDescriptor', 10)->create();

        $mock = $this->withKlinkAdapterMock();

        $mock->shouldReceive('institutions')->andReturn(factory('KlinkDMS\Institution')->make());
        
        $mock->shouldReceive('isNetworkEnabled')->andReturn(false);

        $mock->shouldReceive('search')->andReturnUsing(function($terms, $type, $resultsPerPage, $offset, $facets) use($docs){

            $res = FakeKlinkAdapter::generateSearchResponse($terms, $type, $resultsPerPage, $offset, $facets);

            $res->items = $docs->map(function($i){
                return $i->toKlinkDocumentDescriptor();
            })->toArray();

            return $res;

        });

        $user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
        $this->actingAs($user);

        $url = route( 'documents.recent') . '?s=hello';

        $this->visit( $url )->seePageIs( $url );
        
        $this->assertViewHas('search_replica_parameters', ['s' => 'hello']);

        $this->see(route('documents.recent', ['range' => 'currentweek', 'n' => 12, 's' => 'hello']));
        $this->see(route('documents.recent', ['range' => 'currentweek', 'n' => 24, 's' => 'hello']));
        $this->see(route('documents.recent', ['range' => 'currentweek', 'n' => 50, 's' => 'hello']));
        
        $this->see(route('documents.recent', ['range' => 'today', 's' => 'hello']));
        $this->see(route('documents.recent', ['range' => 'yesterday', 's' => 'hello']));
        $this->see(route('documents.recent', ['range' => 'currentweek', 's' => 'hello']));
        $this->see(route('documents.recent', ['range' => 'currentmonth', 's' => 'hello']));

        $this->see('search-form');

    }

}