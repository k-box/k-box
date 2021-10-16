<?php

namespace Tests\Feature;

use KBox\File;
use KBox\User;
use KBox\Project;
use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use KBox\Publication;
use KBox\DocumentDescriptor;

class DocumentEmbedControllerTest extends TestCase
{
    public function test_embed_is_loaded_for_document_in_project_when_user_has_access_to_the_project()
    {
        $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $project = factory(Project::class)->create();

        $manager = $project->manager;

        $user = User::factory()->partner()->create();
        
        $project->users()->attach($user->id);

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $manager->id]);
        $service->addDocumentToGroup($manager, $document, $project->collection);
        
        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);
        
        $response->assertStatus(200);

        $response->assertViewIs('documents.embed');
    }

    public function test_embed_is_forbidden_if_user_do_not_have_access_to_document_in_project()
    {
        $this->withKlinkAdapterFake();
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $project = factory(Project::class)->create();

        $manager = $project->manager;

        $user = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $manager->id]);
        $service->addDocumentToGroup($manager, $document, $project->collection);
        
        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('errors.403');
    }

    public function test_embed_is_loaded_for_shared_document()
    {
        $user = tap(User::factory()->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER_LIMITED);
        });
        $user_accessing_the_document = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $document->shares()->create([
            'user_id' => $user->id,
            'sharedwith_id' => $user_accessing_the_document->id, //the id
            'sharedwith_type' => get_class($user_accessing_the_document), //the class
            'token' => hash('sha512', '$token_content'),
        ]);

        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('documents.embed');
    }

    public function test_public_document_can_be_previewed_after_login()
    {
        $user = User::factory()->projectManager()->create();
        $user_accessing_the_document = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => true]);
        
        Publication::unguard(); // as fields are not mass assignable
        
        $document->publications()->create([
            'published_at' => Carbon::now()
        ]);
        
        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('documents.embed');
    }

    public function test_public_document_can_be_previewed_without_login()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->projectManager()->create();
        $user_accessing_the_document = User::factory()->partner()->create();

        $service = app('KBox\Documents\Services\DocumentsService');

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => true]);

        Publication::unguard(); // as fields are not mass assignable
        
        $document->publications()->create([
            'published_at' => Carbon::now()
        ]);

        $project1 = factory(Project::class)->create(['user_id' => $user->id]);
        $project1->users()->attach($user_accessing_the_document->id);

        $project1_child1 = $project1->collection;
        $service->addDocumentToGroup($user, $document, $project1_child1);

        $url = route('documents.embed', ['uuid' => $document->uuid]);
        
        $response = $this->get($url);

        $response->assertViewIs('documents.embed');
    }
    
    public function test_document_cannot_be_previewed_if_personal_of_another_user()
    {
        $user = User::factory()->projectManager()->create();
        $user_accessing_the_document = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('errors.403');
    }

    public function test_user_can_preview_own_document()
    {
        $user = User::factory()->projectManager()->create();

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('documents.embed');
    }
    
    public function test_document_preview_is_available_even_if_owner_is_disabled()
    {
        $this->withKlinkAdapterFake();

        $user = User::factory()->projectManager()->create();
        $user_accessing_the_document = User::factory()->partner()->create();
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $project1 = factory(Project::class)->create(['user_id' => $user->id]);
        $project1->users()->attach($user_accessing_the_document->id);

        $project1_child1 = $project1->collection;
        $service->addDocumentToGroup($user, $document, $project1_child1);
        
        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $user->delete();

        $response = $this->actingAs($user_accessing_the_document)->get($url);
        
        $response->assertStatus(200);

        $response->assertViewIs('documents.embed');
    }

    public function test_redirect_to_login_if_document_not_accessible_and_user_not_authenticated()
    {
        $user = User::factory()->projectManager()->create();
        $user_accessing_the_document = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->get($url);

        $response->assertViewIs('errors.login');
    }

    public function test_forbidden_return_if_the_document_is_not_accessible_and_the_user_is_logged_in()
    {
        $user = User::factory()->projectManager()->create();
        $user_accessing_the_document = User::factory()->partner()->create();

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user_accessing_the_document)->get($url);

        $response->assertViewIs('errors.403');
    }

    public function test_not_found_page_is_returned_if_file_is_trashed()
    {
        $user = User::factory()->projectManager()->create();
        
        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);
        
        $document->file_id = null;
        $document->save();
        
        $url = route('documents.embed', ['uuid' => $document->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('errors.404');
    }

    public function test_embed_specific_file_version_is_possible()
    {
        $user = User::factory()->projectManager()->create();

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id, 'is_public' => false]);

        $last_version = $document->file;

        $first_version = factory(File::class)->create([
            'mime_type' => 'text/html',
        ]);

        $last_version->revision_of = $first_version->id;
        $last_version->save();

        $url = route('documents.embed', ['uuid' => $document->uuid, 'versionUuid' => $first_version->uuid]);

        $response = $this->actingAs($user)->get($url);

        $response->assertViewIs('documents.embed');
        $this->assertTrue($response->data('file')->is($first_version));
    }
}
