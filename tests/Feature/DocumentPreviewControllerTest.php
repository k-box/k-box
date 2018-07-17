<?php

namespace Tests\Feature;

use KBox\Option;
use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use KBox\Publication;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);
        
        $response->assertStatus(200);

        $response->assertViewIs('documents.preview');
    }

    public function test_preview_is_forbidden_if_user_do_not_have_access_to_document_in_project()
    {
        $this->withKlinkAdapterFake();
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $project = factory('KBox\Project')->create();

        $manager = $project->manager;

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $manager->id]);
        $service->addDocumentToGroup($manager, $document, $project->collection);
        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('errors.403');
    }

    public function test_preview_is_loaded_for_shared_document()
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

        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('documents.preview');
    }

    public function test_public_document_can_be_previewed_after_login()
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
        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('documents.preview');
    }

    public function test_public_document_can_be_previewed_without_login()
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

        $url = route('documents.preview', ['uuid' => $document->uuid]);
        
        $response = $this->get($url);

        $response->assertViewIs('documents.preview');
    }
    
    public function test_document_cannot_be_previewed_if_personal_of_another_user()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id]);

        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('errors.403');
    }

    public function test_user_can_preview_own_document()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);

        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('documents.preview');
    }
    
    public function test_document_preview_is_available_even_if_owner_is_disabled()
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
        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $user->delete();

        $response = $this->actingAs($user_accessing_the_document)->get($url);
        
        $response->assertStatus(200);

        $response->assertViewIs('documents.preview');
    }

    public function test_redirect_to_login_if_document_not_accessible_and_user_not_authenticated()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);

        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->get($url);

        $response->assertRedirect(route('frontpage'));
        $response->assertSessionHas('url.dms.intended', $url);
    }

    public function test_forbidden_return_if_the_document_is_not_accessible_and_the_user_is_logged_in()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);

        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('errors.403');
    }

    public function test_not_found_page_is_returned_if_file_is_trashed()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);
        
        $document->file_id = null;
        $document->save();
        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('errors.404');
    }

    
    public function test_public_document_can_be_downloaded_by_the_klink_using_the_preview_link()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, true);

        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => true]);
        
        Publication::unguard(); // as fields are not mass assignable
        
        $document->publications()->create([
            'published_at' => Carbon::now()
        ]);
        
        $url = route('documents.preview', ['uuid' => $document->uuid]);

        $response = $this->get($url, ['User-Agent' => 'guzzlehttp']);

        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $document->file->hash);
    }

    public function test_preview_specific_file_version_is_possible()
    {
        $user = tap(factory('KBox\User')->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $document = factory('KBox\DocumentDescriptor')->create(['owner_id' => $user->id, 'is_public' => false]);

        $last_version = $document->file;

        $first_version = factory('KBox\File')->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $url = route('documents.preview', ['uuid' => $document->uuid, 'versionUuid' => $first_version->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('documents.preview');
        $this->assertTrue($response->data('file')->is($first_version));
    }
}
