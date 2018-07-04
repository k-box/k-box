<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use KBox\Publication;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentPreviewControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_preview_is_loaded_for_document_in_project_when_user_has_access_to_the_project()
    {
        $this->withKlinkAdapterFake();
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $project = factory('KBox\Project')->create();

        $manager = $project->manager;

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $project->users()->attach($user->id);       

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $manager->id]);
        $service->addDocumentToGroup($manager, $document, $project->collection);
        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $response = $this->actingAs($user)->get($url);
        
        $response->assertStatus(200);

        $response->assertViewIs('documents.preview');
    }


    public function testDocumentShowForSharedDocument()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id]);

        $document->shares()->create([
            'user_id' => $user->id,
            'sharedwith_id' => $user_accessing_the_document->id, //the id
            'sharedwith_type' => get_class($user_accessing_the_document), //the class
            'token' => hash('sha512', '$token_content'),
        ]);

        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('documents.preview');
    }

    public function testDocumentShowForPublicDocument()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => true]);
        

        Publication::unguard(); // as fields are not mass assignable
        
        $document->publications()->create([
            'published_at' => Carbon::now()
        ]);
        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('documents.preview');
    }

    public function testDocumentShowForPublicDocumentWithNoLogin()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $service = app('Klink\DmsDocuments\DocumentsService');

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => true]);

        Publication::unguard(); // as fields are not mass assignable
        
        $document->publications()->create([
            'published_at' => Carbon::now()
        ]);

        $project1 = factory('KBox\Project')->create(['user_id' => $user->id]);
        $project1->users()->attach($user_accessing_the_document->id);

        $project1_child1 = $project1->collection;
        $service->addDocumentToGroup($user, $document, $project1_child1);

        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);
        
        $response = $this->get($url);

        $response->assertViewIs('documents.preview');
    }
    
    public function testDocumentShowForDocumentNotSharedNorInProject()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id]);

        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('errors.403');
    }

    public function testDocumentShowForPrivateDocumentNotInCollectionOrShared()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);

        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('documents.preview');
    }
    
    public function testDocumentShowForDocumentInProjectWithOwnerDisabled()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id]);

        $project1 = factory('KBox\Project')->create(['user_id' => $user->id]);
        $project1->users()->attach($user_accessing_the_document->id);

        $project1_child1 = $project1->collection;
        $service->addDocumentToGroup($user, $document, $project1_child1);
        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $user->delete();

        $response = $this->actingAs($user_accessing_the_document)->get($url);
        
        $response->assertStatus(200);

        $response->assertViewIs('documents.preview');
    }

    public function testDocumentShowForPrivateDocumentNotInCollectionOrSharedViaOtherUser()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);

        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('errors.403');
    }

    public function testDocumentNotFoundIfFileIsTrashed()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);
        
        $document->file_id = null;
        $document->save();
        
        $url = route('klink_api', ['id' => $document->local_document_id, 'action' => 'document']);
        
        $this->disableExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $response = $this->actingAs($user)->get($url);
    }
}
