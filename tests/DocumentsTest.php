<?php

use Laracasts\TestDummy\Factory;
use KBox\User;
use KBox\File;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\Project;
use Illuminate\Support\Str;
use Tests\BrowserKitTestCase;
use KBox\Documents\Facades\Files;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\Exceptions\KlinkException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;

/*
 * Test something related to document descriptors management
*/
class DocumentsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function user_provider_for_editpage_public_checkbox_test()
    {
        return [
            [Capability::$ADMIN, true],
            [[Capability::MANAGE_KBOX], false],
            [Capability::$PROJECT_MANAGER, true],
            [Capability::$PARTNER, false],
        ];
    }

    public function user_provider_document_link_test()
    {
        return [
            [Capability::$ADMIN, true],
            [Capability::$PROJECT_MANAGER, true],
            [Capability::$PARTNER, false],
        ];
    }
    
    public function vibility_provider()
    {
        return [
            ['public'],
            ['private'],
        ];
    }

    public function user_provider_that_can_make_public()
    {
        return [
            [Capability::$ADMIN],
            [Capability::$PROJECT_MANAGER],
        ];
    }
    
    public function user_provider_document_link_login_with_second_user_test()
    {
        return [
            [Capability::$ADMIN, Capability::$ADMIN],
            [Capability::$ADMIN, Capability::$PROJECT_MANAGER],
            [Capability::$ADMIN, Capability::$PARTNER],
            [Capability::$PROJECT_MANAGER, Capability::$ADMIN],
            [Capability::$PROJECT_MANAGER, Capability::$PROJECT_MANAGER],
            [Capability::$PROJECT_MANAGER, Capability::$PARTNER],
            [Capability::$PARTNER, Capability::$PARTNER],
            [Capability::$PARTNER, Capability::$ADMIN],
            [Capability::$PARTNER, Capability::$PROJECT_MANAGER],
        ];
    }

    public function data_provider_for_facets_composer()
    {
        return [
            [[], ''],
            [['s' => 'pasture'], '?s=pasture'],
        ];
    }

    /**
     * Test that the route for documents.show is not leaking private documents to anyone
     *
     * @return void
     */
    public function testDocumentShowPage()
    {
        // create a document
        
        $user = $this->createAdminUser();
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
        ]);
        
        // test without login
        
        $url = route('documents.show', $doc->id);
        
        $this->visit($url)->seePageIs(route('frontpage'));

        // test with login with the owner user
        
        $this->actingAs($user);
        
        $this->visit($url)->seePageIs(route('documents.preview', ['uuid' => $doc->uuid]));
        
        $this->assertResponseOk();
    }
    
    /**
     * Tests if the edit page is showed even if the user uploader of the document was disabled
     *
     * @dataProvider user_provider_for_editpage_public_checkbox_test
     * @return void
     */
    public function testDocumentEditPageWhenUserDisabled($caps, $can_see)
    {
        $user = $this->createUser($caps);
        $user2 = $this->createUser($caps);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
        ]);

        $user->delete();

        $url = route('documents.edit', $doc->id);
        
        $this->actingAs($user2);
        
        $this->visit($url);
        
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
    public function testDocumentUpdate($caps, $ignored)
    {
        $this->withKlinkAdapterFake();
        
        $user = $this->createUser($caps);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'copyright_usage' => 'C',
            'copyright_owner' => collect(['name' => 'owner name', 'website' => 'https://something.com'])
        ]);
        
        $url = route('documents.edit', $doc->id);
        
        $this->actingAs($user);
        
        $this->visit($url);
        
        $this->type('Document new Title', 'title');
        
        $this->press(trans('actions.save'));
        
        $this->seePageIs($url);
        
        $this->see(trans('documents.messages.updated'));
        
        $this->see('Document new Title');
    }
    
    /**
     * @dataProvider user_provider_that_can_make_public
     */
    public function testDocumentUpdateRemoveCollectionFromPublicDocument($caps)
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser($caps);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'is_public' => true,
            'hash' => $file->hash
        ]);
        
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $group = $service->createGroup($user, 'Personal collection of user '.$user->id);
        
        $group->documents()->save($doc);
        
        $this->actingAs($user);
        
        \Session::start(); // Start a session for the current test

        $this->json('PUT', route('documents.update', ['id' => $doc->id]), [
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
    public function testDocumentUpdateAddCollectionToPublicDocument($caps)
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser($caps);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'is_public' => true,
            'hash' => $file->hash,
            'copyright_usage' => 'C',
            'copyright_owner' => collect(['name' => 'owner name', 'website' => 'https://something.com'])
        ]);
        
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $group = $service->createGroup($user, 'Personal collection of user '.$user->id);
        
        $this->actingAs($user);
        
        \Session::start(); // Start a session for the current test

        $this->json('PUT', route('documents.update', ['id' => $doc->id]), [
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
    public function testDocumentLinkLoginRedirect_NotLoggedIn($cap, $ignored)
    {
        $this->withKlinkAdapterFake();
        
        // create a document
        
        $user_password = Str::random(10);
        
        $user = $this->createUser($cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id
        ]);
        
        $doc->document_uri = route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        
        $doc->save();
        
        // get the link
        $doc_link = \DmsRouting::preview($doc);
        
        // goto link, see login page
        $this->visit($doc_link)->seePageIs(route('frontpage'));
        
        // perform login
        $this->type($user->email, 'email');
        $this->type($user_password, 'password');
        
        $this->press(trans('login.form.submit'));
        
        // see document page
        
        $this->seePageIs($doc_link);
    }
    
    /**
     * Tests the document link for an already logged-in user
     *
     * @dataProvider user_provider_document_link_test
     */
    public function testDocumentLinkLoginRedirect_LoggedIn($cap, $ignored)
    {
        $this->withKlinkAdapterFake();
        
        // create a document
        
        $user_password = Str::random(10);
        
        $user = $this->createUser($cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id
        ]);
        
        $doc->document_uri = route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        
        $doc->save();
        
        // get the link
        $doc_link = \DmsRouting::preview($doc);
        
        $this->actingAs($user);
        
        // goto link, see linked page
        $this->visit($doc_link)->seePageIs($doc_link);
    }
    
    /**
     * Tests if when using a document link, after the login process has been done, works as expected
     *
     * @dataProvider user_provider_document_link_test
     */
    public function testDocumentLinkLoginRedirect_LoginThen($cap, $ignored)
    {
        $this->markTestSkipped(
            'Seems to get the runner stuck on Gitlab and Travis CI.'
        );

        $this->withKlinkAdapterFake();
        
        // create a document
        
        $user_password = Str::random(10);
        
        $user = $this->createUser($cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id
        ]);
        
        $doc->document_uri = route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        
        $doc->save();
        
        // get the link
        $doc_link = \DmsRouting::preview($doc);
        
        // goto link, see login page
        $this->visit(route('frontpage'))->seePageIs(route('frontpage'));
        
        // perform login
        $this->type($user->email, 'email');
        $this->type($user_password, 'password');
        
        $this->press(trans('login.form.submit'));
        
        // see document page
        
        $this->visit($doc_link)->seePageIs($doc_link);
    }
    
    /**
     * Tests if when using a document link, after the login process has been done, works as expected
     *
     * @dataProvider user_provider_document_link_login_with_second_user_test
     */
    public function testDocumentLinkLoginRedirect_LoginWithSecondUser($cap, $second_user_cap)
    {
        $this->withKlinkAdapterFake();
        
        // create a document
        
        $user_password = Str::random(10);
        
        $owner = $this->createUser($cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
        $user = $this->createUser($second_user_cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $owner->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $owner->id,
            'file_id' => $file->id
        ]);
        
        $doc->document_uri = route('klink_api', ['id' => $doc->local_document_id, 'action' => 'document']);
        
        $doc->save();
        
        // get the link
        $doc_link = \DmsRouting::preview($doc);
        
        // goto link, see login page
        $this->visit($doc_link)->seePageIs(route('frontpage'));
        
        // perform login
        $this->type($user->email, 'email');
        $this->type($user_password, 'password');
        
        $this->press(trans('login.form.submit'));
        
        // see document page
        
        $this->seePageIs($doc_link);
    }
    
    /**
     * Test the conversion from KBox\DocumentDescriptor to KlinkDocumentDescriptor
     * @dataProvider vibility_provider
     */
    public function testDocumentDescriptorToKlinkDocumentDescriptor($visibility)
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'is_public' => $visibility === 'private' ? false : true
        ]);
        
        $service = app('KBox\Documents\Services\DocumentsService');
        
        $group = $service->createGroup($user, 'Personal collection of user '.$user->id);
        
        $group->documents()->save($doc);
        
        $descriptor = $doc->toKlinkDocumentDescriptor($visibility === 'private' ? false : true);
        
        $this->assertNotNull($descriptor);

        $data = $descriptor->toData();
        
        $this->assertEquals($visibility, $descriptor->visibility());
        
        $this->assertEquals($doc->title, $data->properties->title);
        
        $this->assertEquals($doc->hash, $descriptor->hash(), 'Descriptor hash not equal to DocumentDescriptor');
        $this->assertEquals($doc->hash, $file->hash, 'File Hash not equal to DocumentDescriptor hash');
        
        $collections = $descriptor->collections();

        if ($visibility === 'private') {
            $this->assertNotEmpty($collections);
            $this->assertEquals($group->toKlinkGroup(), $collections[0]);
        } else {
            $this->assertEmpty($collections);
        }
    }
    
    public function testDocumentReindexingStartedByAUserThatIsNotTheOwner()
    {
        $fake = $this->withKlinkAdapterFake();
        
        $user = $this->createUser(Capability::$PROJECT_MANAGER);
                
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'is_public' => false,
            'copyright_usage' => 'C',
            'copyright_owner' => collect(['name' => 'owner name', 'website' => 'https://something.com'])
        ]);
        
        $service = app('KBox\Documents\Services\DocumentsService');
        
        // first indexing, like the one after the upload
        $service->reindexDocument($doc, 'private');
        
        // Search for it, must only be indexed once
        
        $fake->assertDocumentIndexed($doc->uuid);
        
        // Pick another user
        
        $second_user = $this->createUser(Capability::$PARTNER);
        
        // make an edit to the document, save it and then reindex
        
        $url = route('documents.edit', $doc->id);
        
        $this->actingAs($second_user);
        
        $this->visit($url);
        
        $this->type('Document new Title', 'title');
        
        $this->press(trans('actions.save'));
        
        $this->seePageIs($url);
        
        $this->see(trans('documents.messages.updated'));
        
        $this->see('Document new Title');
        
        $doc = DocumentDescriptor::findOrFail($doc->id);
        
        $fake->assertDocumentIndexed($doc->uuid, 2);
    }

    public function testDocumentService_deleteDocument()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);

        $doc = $this->createDocument($user);

        $service = app('KBox\Documents\Services\DocumentsService');

        $is_deleted = $service->deleteDocument($user, $doc);

        $this->assertTrue($is_deleted);

        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);

        $this->assertTrue($doc->trashed());

        $this->assertTrue($doc->file->trashed());

        $file = File::withTrashed()->findOrFail($doc->file_id);

        $this->assertTrue($file->trashed());
    }

    /**
     * @expectedException KBox\Exceptions\ForbiddenException
     */
    public function testDocumentService_deleteDocument_forbidden()
    {
        $user = $this->createUser([Capability::RECEIVE_AND_SEE_SHARE]);

        $doc = $this->createDocument($user);

        $service = app('KBox\Documents\Services\DocumentsService');

        $service->deleteDocument($user, $doc);
    }

    public function testDocumentDelete()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);

        $doc = $this->createDocument($user);

        \Session::start();

        $url = route('documents.destroy', ['id' => $doc->id,
                '_token' => csrf_token()]);
        
        $this->actingAs($user);

        $this->delete($url);

        $this->see('ok');
        
        $this->assertResponseStatus(202);

        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);

        $this->assertTrue($doc->trashed());
    }

    public function testDocumentService_permanentlyDeleteDocument()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $doc = $this->createDocument($user);

        $service = app('KBox\Documents\Services\DocumentsService');

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
     * @expectedException KBox\Exceptions\ForbiddenException
     */
    public function testDocumentService_permanentlyDeleteDocument_forbidden()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);

        $doc = $this->createDocument($user);

        $service = app('KBox\Documents\Services\DocumentsService');

        $service->deleteDocument($user, $doc); // put doc in trash

        $is_deleted = $service->permanentlyDeleteDocument($user, $doc);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testDocumentForceDelete()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $doc = $this->createDocument($user);

        \Session::start();

        $url = route('documents.destroy', ['id' => $doc->id,
                '_token' => csrf_token()]);
        
        $this->actingAs($user);

        $this->delete($url);

        $this->see('ok');
        
        $this->assertResponseStatus(202);

        $url = route('documents.destroy', [
                'id' => $doc->id,
                'force' => true,
                '_token' => csrf_token()]);

        $this->delete($url);

        $this->see('ok');
        
        $this->assertResponseStatus(202);

        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);
    }

    public function testNewDocumentVersionUpload()
    {
        $this->withKlinkAdapterFake();

        // upload a document (faked by already existing entry in the database)

        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $doc = $this->createDocument($user);

        // create a new version, with different mime_type

        $new_path = base_path('tests/data/example-presentation-simple.pptx');
        $new_mime_type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        $new_document_type = 'presentation';
        $new_hash = Files::hash(base_path('tests/data/example-presentation-simple.pptx'));

        $this->actingAs($user);
        
        $this->visit(route('documents.edit', $doc->id))
          ->see(trans('documents.versions.new_version_button'))
          ->attach(base_path('tests/data/example-presentation-simple.pptx'), 'document')
          ->press(trans('actions.save') /*trans('documents.versions.new_version_button')*/)
          ->see(trans('documents.messages.updated'))
          ->dontSee('documents.messages.updated');

        // check change to: mime_type, document_type, hash and file_id. No changes should be applied to local_document_id

        $updated_descriptor = $doc->fresh();

        $this->assertEquals($updated_descriptor->mime_type, $new_mime_type, 'Mime type not matching');
        $this->assertEquals($updated_descriptor->document_type, $new_document_type, 'Document type not matching');
        $this->assertEquals($updated_descriptor->hash, $new_hash, 'Hash not matching');
        $this->assertEquals($updated_descriptor->local_document_id, $doc->local_document_id, 'Local document ID not matching');
    }

    public function testDocumentDescriptorFileRelation()
    {
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

    public function testFileRevisions()
    {
        $user = $this->createAdminUser();

        $descr = $this->createDocument($user);
        
        $file = $descr->file;

        $revision_of_revision = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $file->path,
            'revision_of' => null,
        ]);

        $revision = factory(\KBox\File::class)->create([
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
     * @dataProvider data_provider_for_facets_composer
     */
    public function testFacetsViewComposerUrlConstruction($search_parameters, $expected_url)
    {
        $this->withKlinkAdapterFake();

        $project_collection_names = ['Project Root', 'Project First Level', 'Second Level'];

        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $project = null;
        $project_group = null;
        $project_group_root = null;
        $project_childs = count($project_collection_names);

        foreach ($project_collection_names as $index => $name) {
            $project_group = $service->createGroup($user, $name, null, $project_group, false);

            if ($index === 0) {
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

        $url = route('documents.groups.show', array_merge(['id' => $project_group->id], $search_parameters));

        $this->visit($url)->seePageIs($url);

        // The next 4 lines are an hack to get the view data enhanced by the composer
        // The final view will not have this data, because the facets.blade.php template
        // is internal to the page view, therefore already rendered when the response ends

        $view = $this->response->original; // is a view
        $composer = app(\KBox\Http\Composers\DocumentsComposer::class);
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
     * This test simulates a "KlinkException(code: 414): Request-URI Too Long" when
     * contructing the facets for the personal section. The page should load, but with no elastic list
     */
    public function testPersonalSectionShowedWhenKlinkException()
    {
        $user = $this->createUser(Capability::$PARTNER);
        $this->actingAs($user);

        $mock = $this->withKlinkAdapterMock();

        $mock->shouldReceive('institutions')->andReturn(factory(\KBox\Institution::class)->make());
        
        $mock->shouldReceive('isNetworkEnabled')->andReturn(false);

        $mock->shouldReceive('search')->andReturnUsing(function ($terms, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $page = 1, $facets = null) {
            $res = FakeKlinkAdapter::generateSearchResponse($terms, $type, $resultsPerPage, $offset, $facets);

            throw new KlinkException('raised error');

            return null;
        });

        $url = route('documents.index').'/personal';

        $this->visit($url)->seePageIs($url);
    }
}
