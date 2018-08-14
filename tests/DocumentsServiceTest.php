<?php

use Laracasts\TestDummy\Factory;
use KBox\User;
use KBox\Group;
use KBox\Capability;
use KBox\DocumentDescriptor;
use Klink\DmsAdapter\KlinkVisibilityType;
use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test something related to document descriptors management
*/
class DocumentsServiceTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function user_provider_with_guest()
    {
        return [
            [Capability::$ADMIN, 'admin'],
            [Capability::$PROJECT_MANAGER, 'project_manager'],
            [Capability::$PARTNER, 'partner'],
            [Capability::$GUEST, 'guest'],
        ];
    }

    public function user_provider_no_guest()
    {
        return [
            [Capability::$ADMIN, 'admin'],
            [Capability::$PROJECT_MANAGER, 'project_manager'],
            [Capability::$PARTNER, 'partner'],
        ];
    }

    /**
     * cover issue https://git.klink.asia/klinkdms/dms/issues/569
     * @dataProvider user_provider_no_guest
     */
    public function testGetDocumentCollections($caps)
    {
        $adapter = $this->withKlinkAdapterFake();
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $user = $this->createUser($caps);
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id
        ]);

        $owned_project = factory(\KBox\Project::class)->create([
            'user_id' => $user->id,
        ]);

        $other_project = factory(\KBox\Project::class)->create();

        $secondary_project = factory(\KBox\Project::class)->create();

        $secondary_project->users()->save($user);
        
        $group = $service->createGroup($user, 'Personal collection of user '.$user->id);
        
        $group->documents()->save($doc);

        Group::findOrFail($owned_project->collection_id)->documents()->save($doc);

        Group::findOrFail($secondary_project->collection_id)->documents()->save($doc);

        // simulate another user, who has access to both projects, that
        // is adding the document to the second project
        Group::findOrFail($other_project->collection_id)->documents()->save($doc);
        
        // now get the collections of the doc
        $collections = $service->getDocumentCollections($doc, $user);

        $this->assertEquals($user->isDMSManager() ? 4 : 3, $collections->count());
    }

    /**
     * issue https://git.klink.asia/klinkdms/dms/issues/690
     */
    public function testFallbackDocumentReIndexingAfterForceHashCheckOnKCore()
    {
        $this->withKlinkAdapterFake();

        // todo test add and reindex
        $user = $this->createAdminUser();

        $descr = $this->createDocument($user);
        
        $file = $descr->file;

        // change the document to a complete non-sense mime-type to
        // force a fallback text extraction
        $descr->mime_type = 'text/basiliscus';
        $file->mime_type = 'text/basiliscus';
        $file->save();
        $file = $file->fresh();
        $descr->save();
        $descr = $descr->fresh();

        $service = app('Klink\DmsDocuments\DocumentsService');

        $ret = $service->reindexDocument($descr, 'private');

        $this->assertInstanceOf(DocumentDescriptor::class, $ret);
    }

    /**
     * issue https://git.klink.asia/klinkdms/dms/issues/690
     */
    public function testFallbackDocumentIndexingAfterForceHashCheckOnKCore()
    {
        $this->withKlinkAdapterFake();

        // todo test add and reindex
        $user = $this->createAdminUser();
        
        $file = factory(\KBox\File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        ;

        // change the document to a complete non-sense mime-type to
        // force a fallback text extraction

        $file->mime_type = 'text/basiliscus';
        $file->save();
        $file = $file->fresh();

        $service = app('Klink\DmsDocuments\DocumentsService');

        $ret = $service->indexDocument($file, 'private', $user);

        $this->assertInstanceOf(DocumentDescriptor::class, $ret);
    }

    /**
     * Test removing a private and public document from the public network
     */
    public function testDeletePublicDocument()
    {
        $fake = $this->withKlinkAdapterFake();
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $user = $this->createAdminUser();

        $descr = $this->createDocument($user);
        $service->reindexDocument($descr);

        $descr->is_public = true;
        $descr->save();
        $service->reindexDocument($descr, KlinkVisibilityType::KLINK_PUBLIC);

        $fake->assertDocumentIndexed($descr->uuid, 2);

        $this->assertNotNull($fake->getDocument($descr->uuid, 'public'), 'Null public get');
        $this->assertNotNull($fake->getDocument($descr->uuid, 'private'), 'Null private get');

        $descr->is_public = false;
        $descr->save();

        $service->deletePublicDocument($descr);

        $fake->assertDocumentRemoved($descr->uuid, 'public', 1);
        $fake->assertDocumentRemoved($descr->uuid, 'private', 0);

        $this->assertNull($fake->getDocument($descr->uuid, 'public'), 'NOT Null public get');
        $this->assertNotNull($fake->getDocument($descr->uuid, 'private'), 'Null private get');
    }
}
