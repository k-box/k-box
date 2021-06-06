<?php

namespace Tests\Feature;

use KBox\Option;
use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use KBox\Publication;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentDownloadControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_download_for_document_in_project_when_user_has_access_to_the_project()
    {
        $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $project = factory(\KBox\Project::class)->create();

        $manager = $project->manager;

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $project->users()->attach($user->id);

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $manager->id]);
        $service->addDocumentToGroup($manager, $document, $project->collection);
        
        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);
        
        $response->assertStatus(200);
        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $document->file->hash);
    }

    public function test_download_forbidden_if_user_do_not_have_access_to_document_in_project()
    {
        $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $project = factory(\KBox\Project::class)->create();

        $manager = $project->manager;

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $manager->id]);
        $service->addDocumentToGroup($manager, $document, $project->collection);
        
        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
    }

    public function test_download_for_shared_document()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER_LIMITED);
        });
        $user_accessing_the_document = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $document->shares()->create([
            'user_id' => $user->id,
            'sharedwith_id' => $user_accessing_the_document->id, //the id
            'sharedwith_type' => get_class($user_accessing_the_document), //the class
            'token' => hash('sha512', '$token_content'),
        ]);

        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertStatus(200);
        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $document->file->hash);
    }

    public function test_public_document_can_be_downloaded_after_login()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => true]);
        
        Publication::unguard(); // as fields are not mass assignable
        
        $document->publications()->create([
            'published_at' => Carbon::now()
        ]);
        
        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertStatus(200);
        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $document->file->hash);
    }

    public function test_public_document_can_be_downloaded_without_login()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $service = app('KBox\Documents\Services\DocumentsService');

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => true]);

        Publication::unguard(); // as fields are not mass assignable
        
        $document->publications()->create([
            'published_at' => Carbon::now()
        ]);

        $project1 = factory(\KBox\Project::class)->create(['user_id' => $user->id]);
        $project1->users()->attach($user_accessing_the_document->id);

        $project1_child1 = $project1->collection;
        $service->addDocumentToGroup($user, $document, $project1_child1);

        $url = route('documents.download', ['uuid' => $document->uuid]);
        
        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $document->file->hash);
    }
    
    public function test_document_cannot_be_downloaded_if_personal_of_another_user()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertStatus(403);
    }

    public function test_user_can_download_own_document()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $document->file->hash);
    }
    
    public function test_document_download_is_possible_even_if_owner_is_disabled()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $project1 = factory(\KBox\Project::class)->create(['user_id' => $user->id]);
        $project1->users()->attach($user_accessing_the_document->id);

        $project1_child1 = $project1->collection;
        $service->addDocumentToGroup($user, $document, $project1_child1);
        
        $url = route('documents.download', ['uuid' => $document->uuid]);

        $user->delete();

        $response = $this->actingAs($user_accessing_the_document)->get($url);
        
        $response->assertStatus(200);
        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $document->file->hash);
    }

    public function test_redirect_to_login_if_document_not_accessible_and_user_not_authenticated()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->get($url);

        $response->assertRedirect(route('frontpage'));
    }

    public function test_forbidden_return_if_the_document_is_not_accessible_and_the_user_is_logged_in()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        $user_accessing_the_document = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertStatus(403);
    }

    public function test_not_found_page_is_returned_if_file_is_trashed()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });
        
        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);
        
        $document->file_id = null;
        $document->save();
        
        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(404);
    }

    public function test_public_document_can_be_downloaded_by_the_klink_using_the_download_link()
    {
        Option::put(Option::PUBLIC_CORE_ENABLED, true);

        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $document = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => true]);
        
        Publication::unguard(); // as fields are not mass assignable
        
        $document->publications()->create([
            'published_at' => Carbon::now()
        ]);
        
        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->get($url, ['User-Agent' => 'guzzlehttp']);

        // $this->assertInstanceOf(BinaryFileResponse::class ,$response->baseResponse);

        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $document->file->hash);
    }

    public function test_head_requests_are_supported()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();

        $file = factory(\KBox\File::class)->create([
            'mime_type' => 'application/pdf',
        ]);
        
        $document = factory(\KBox\DocumentDescriptor::class)->create([
            'file_id' => $file->id
        ]);

        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($document->owner)->call('HEAD', $url);

        $response->assertStatus(200);
        $response->assertHeader('Content-Length', $file->size);
        $response->assertHeader('Content-Type', $file->mime_type);
        $response->assertHeader('ETag', $document->file->hash);
    }
    
    public function test_download_returns_last_file_version()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();
        
        $document = factory(\KBox\DocumentDescriptor::class)->create();
        
        $last_version = $document->file;

        $first_version = factory(\KBox\File::class)->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $url = route('documents.download', ['uuid' => $document->uuid]);

        $response = $this->actingAs($document->owner)->get($url);

        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $last_version->hash);
    }
    
    public function test_file_version_can_be_downloaded()
    {
        Storage::fake('local');
        $adapter = $this->withKlinkAdapterFake();
        
        $document = factory(\KBox\DocumentDescriptor::class)->create();
        
        $last_version = $document->file;

        $first_version = factory(\KBox\File::class)->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $url = route('documents.download', ['uuid' => $document->uuid, 'versionUuid' => $first_version]);

        $response = $this->actingAs($document->owner)->get($url);

        $response->assertInstanceOf(BinaryFileResponse::class);
        $response->assertHeader('ETag', $first_version->hash);
    }
}
