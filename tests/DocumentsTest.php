<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Project;
use Carbon\Carbon;
use KlinkDMS\Flags;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Klink\DmsAdapter\KlinkSearchRequest;
use Klink\DmsAdapter\KlinkSearchResults;
use Tests\BrowserKitTestCase;
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
            [Capability::$DMS_MASTER, false],
            [Capability::$PROJECT_MANAGER, true],
            [Capability::$PARTNER, false],
            [Capability::$GUEST, false],
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

    public function recent_date_range_provider()
    {
        return [
            ['today'],
            ['yesterday'],
            ['currentweek'],
            ['currentmonth'],
        ];
    }

    public function recent_items_per_page_provider()
    {
        return [
            [5],
            [10],
            [15],
            [25],
            [50],
        ];
    }

    public function recent_sorting_provider()
    {
        return [
            ['a', 'ASC'],
            ['d', 'DESC'],
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
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
        ]);
        
        // test without login
        
        $url = route('documents.show', $doc->id);
        
        $this->visit($url)->seePageIs(route('frontpage'));

        
        // test with login with the owner user
        
        $this->actingAs($user);
        
        $this->visit($url)->seePageIs(route('klink_api', ['id' => $doc->local_document_id, 'action' => 'preview']));
        
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
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
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
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id
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
        
        $user_password = str_random(10);
        
        $user = $this->createUser($cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
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
        
        $user_password = str_random(10);
        
        $user = $this->createUser($cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
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
        $this->visit($doc_link)->seePageIs($doc_link);
    }
    
    /**
     * Tests if when using a document link, after the login process has been done, works as expected
     *
     * @dataProvider user_provider_document_link_test
     */
    public function testDocumentLinkLoginRedirect_LoginThen($cap, $ignored)
    {
        $this->withKlinkAdapterFake();
        
        // create a document
        
        $user_password = str_random(10);
        
        $user = $this->createUser($cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
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
        
        $user_password = str_random(10);
        
        $owner = $this->createUser($cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
        $user = $this->createUser($second_user_cap, [ //Capability::$PROJECT_MANAGER
            'password' => bcrypt($user_password)
        ]);
        
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
        $this->visit($doc_link)->seePageIs(route('frontpage'));
        
        // perform login
        $this->type($user->email, 'email');
        $this->type($user_password, 'password');
        
        $this->press(trans('login.form.submit'));
        
        
        // see document page
        
        $this->seePageIs($doc_link);
    }
    
    /**
     * Test the conversion from KlinkDMS\DocumentDescriptor to KlinkDocumentDescriptor
     * @dataProvider vibility_provider
     */
    public function testDocumentDescriptorToKlinkDocumentDescriptor($visibility)
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);
        
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'is_public' => $visibility === 'private' ? false : true
        ]);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
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
                
        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'is_public' => false
        ]);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
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

        $service = app('Klink\DmsDocuments\DocumentsService');

        $is_deleted = $service->deleteDocument($user, $doc);

        $this->assertTrue($is_deleted);

        $doc = DocumentDescriptor::withTrashed()->findOrFail($doc->id);

        $this->assertTrue($doc->trashed());

        $this->assertTrue($doc->file->trashed());

        $file = File::withTrashed()->findOrFail($doc->file_id);

        $this->assertTrue($file->trashed());
    }

    /**
     * @expectedException KlinkDMS\Exceptions\ForbiddenException
     */
    public function testDocumentService_deleteDocument_forbidden()
    {
        $user = $this->createUser(Capability::$GUEST);

        $doc = $this->createDocument($user);

        $service = app('Klink\DmsDocuments\DocumentsService');

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
    public function testDocumentService_permanentlyDeleteDocument_forbidden()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);

        $doc = $this->createDocument($user);

        $service = app('Klink\DmsDocuments\DocumentsService');

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

        $new_path = base_path('tests/data/example-presentation.pptx');
        $new_mime_type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        $new_document_type = 'presentation';
        $new_hash = KlinkDocumentUtils::generateDocumentHash(base_path('tests/data/example-presentation.pptx'));

        $this->actingAs($user);
        
        $this->visit(route('documents.edit', $doc->id))
          ->see(trans('documents.versions.new_version_button'))
          ->attach(base_path('tests/data/example-presentation.pptx'), 'document')
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
     * @dataProvider data_provider_for_facets_composer
     */
    public function testFacetsViewComposerUrlConstruction($search_parameters, $expected_url)
    {
        $this->withKlinkAdapterFake();

        $project_collection_names = ['Project Root', 'Project First Level', 'Second Level'];

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        
        $service = app('Klink\DmsDocuments\DocumentsService');

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
    public function testRecentPageRangeSelection($range)
    {
        Flags::enable(Flags::UNIFIED_SEARCH);

        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        $this->actingAs($user);
        
        // goto link, see linked page

        $url = route('documents.recent', ['range' => $range]);

        $this->visit($url)->seePageIs($url);

        $this->assertViewHas('range', $range);

        $user = $user->fresh();
        $this->assertEquals($range, $user->optionRecentRange());
    }
    
    /**
     * Test Items per page option is honored
     *
     * @dataProvider recent_items_per_page_provider
     */
    public function testRecentPageItemsPerPageSelection($items_per_page)
    {
        Flags::enable(Flags::UNIFIED_SEARCH);

        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        $this->actingAs($user);

        for ($i=0; $i < $items_per_page; $i++) {
            $this->createDocument($user);
        }

        $url = route('documents.recent').'?n='.$items_per_page ;

        $this->visit($url)->seePageIs($url);
        $user = $user->fresh();
        $this->assertEquals($items_per_page, $user->optionItemsPerPage());
        
        $this->assertEquals($items_per_page,
            $this->response->original->documents->values()->collapse()->count());
    }
    
    /**
     * Test sorting option is honored
     *
     * @dataProvider recent_sorting_provider
     */
    public function testRecentPageSortingSelection($option, $expected_value)
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        $this->actingAs($user);

        // for ($i=0; $i < $items_per_page; $i++) {
        //     $this->createDocument($user);
        // }

        $url = route('documents.recent').'?o='.$option ;

        $this->visit($url)->seePageIs($url);
        
        $this->assertViewHas('order', $expected_value);
    }
    
    /**
     * Test recent page contains expected documents
     *
     * - last updated by user
     * - last shared with me
     */
    public function testRecentPageContainsExpectedDocuments($range = 'today')
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        $user2 = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        
        $this->actingAs($user);

        $documents = collect(); // documents created by $user
        $documents_user2 = collect();  // documents created by $user2

        $count_documents_by_me = 5;
        $count_documents_shared_with_me = 1;
        $count_documents_in_project = 1;

        // create some documents for $user

        for ($i=0; $i < $count_documents_by_me; $i++) {
            $documents->push($this->createDocument($user));
        }
        

        $doc = null;
        
        // create a project using $user2, add 1 document in the project
        $project1 = $this->createProject(['user_id' => $user2->id]);
        $project1->users()->attach($user->id);
        $doc = $this->createDocument($user2);
        $service = app('Klink\DmsDocuments\DocumentsService');
        $service->addDocumentToGroup($user2, $doc, $project1->collection);
        $doc = $doc->fresh();
        $documents_user2->push($doc);

        // create a second user, share with the first one a couple of documents
        for ($i=0; $i < $count_documents_shared_with_me; $i++) {
            $doc = $this->createDocument($user2);

            $doc->shares()->create([
                        'user_id' => $user2->id,
                        'sharedwith_id' => $user->id, //the id
                        'sharedwith_type' => get_class($user), //the class
                        'token' => hash('sha512', $doc->id),
                    ]);
            $documents_user2->push($doc);
        }

        // grab the last from $documents and change its updated_at to yesterday
        // (wrt the selected $range)
        $last = $documents->last();

        $last->updated_at = Carbon::yesterday();

        $last->timestamps = false; //temporarly disable the automatic upgrade of the updated_at field

        $last->save();

        $url = route('documents.recent', ['range' => $range]);

        $this->visit($url)->seePageIs($url);

        $this->assertEquals(($count_documents_by_me - 1) + $count_documents_shared_with_me + $count_documents_in_project,
            $this->response->original->documents->values()->collapse()->count());
    }

    public function testRecentPageSearchWithFilters()
    {
        Flags::enable(Flags::UNIFIED_SEARCH);

        $docs = factory('KlinkDMS\DocumentDescriptor', 10)->create();

        $adapter = $this->withKlinkAdapterFake();

        // prepare the request
        $searchRequest = KlinkSearchRequest::build('*', 'private', 1, 1, [], []);
        
        // prepare some fake results
        $adapter->setSearchResults('private', KlinkSearchResults::fake($searchRequest, []));

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        $this->actingAs($user);

        $url = route('documents.recent').'?s=hello';

        $this->visit($url)->seePageIs($url);
        
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

    /**
     * This test simulates a "KlinkException(code: 414): Request-URI Too Long" when
     * contructing the facets for the personal section. The page should load, but with no elastic list
     */
    public function testPersonalSectionShowedWhenKlinkException()
    {
        $user = $this->createUser(Capability::$PARTNER);
        $this->actingAs($user);

        $mock = $this->withKlinkAdapterMock();

        $mock->shouldReceive('institutions')->andReturn(factory('KlinkDMS\Institution')->make());
        
        $mock->shouldReceive('isNetworkEnabled')->andReturn(false);

        $mock->shouldReceive('search')->andReturnUsing(function ($terms, $type, $resultsPerPage, $offset, $facets) {
            $res = FakeKlinkAdapter::generateSearchResponse($terms, $type, $resultsPerPage, $offset, $facets);

            throw new KlinkException('raised error');

            return null;
        });

        $url = route('documents.index').'/personal';

        $this->visit($url)->seePageIs($url);
    }
}
