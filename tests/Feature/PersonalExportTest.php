<?php

namespace Tests\Feature;

use KBox\User;
use KBox\File;
use KBox\Group;
use KBox\Starred;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use KBox\PersonalExport;
use KBox\DocumentDescriptor;
use KBox\Event\PersonalExportReady;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use KBox\Jobs\PreparePersonalExportJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalExportTest extends TestCase
{
    use DatabaseTransactions;

    private function createExportableDataForUser($user)
    {
        $file = factory(File::class)->create([
            'user_id' => $user->id,
            'original_uri' => ''
        ]);
        
        $doc = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
        ]);

        $starred = factory(Starred::class)->create([
            'user_id' => $user->id,
            'document_id' => $doc->id,
        ]);

        $project = factory(Project::class)->create(['user_id' => $user->id]);

        $collection = factory(Group::class)->create([
            'user_id' => $user->id,
            'is_private' => true
        ]);
    }

    public function test_personal_export_can_be_requested()
    {
        Queue::fake();
        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $url = route('profile.data-export.store');

        $response = $this->actingAs($user)->from(route('profile.data-export.index'))->post($url);

        $response->assertRedirect(route('profile.data-export.index'));

        $export = PersonalExport::ofUser($user)->first();

        $this->assertNotNull($export);

        Queue::assertPushed(PreparePersonalExportJob::class, function ($job) use ($user, $export) {
            return $job->export->id === $export->id && $job->export->user_id === $user->id;
        });
    }
    
    public function test_personal_export_cannot_be_requested_twice_within_60_minutes()
    {
        Queue::fake();
        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $pending_export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subMinute(),
            'purge_at' => now()->addMinutes(15)
        ]);
        
        $last_completed_export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subMinute(),
            'generated_at' => now(),
            'purge_at' => now()->addMinutes(15)
        ]);

        $url = route('profile.data-export.store');

        $response = $this->actingAs($user)->from(route('profile.data-export.index'))->post($url);

        $response->assertRedirect(route('profile.data-export.index'));

        $response->assertSessionHasErrors(['time']);

        Queue::assertNothingPushed();
    }

    public function test_personal_exports_can_be_listed()
    {
        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
        ]);
        $expired_export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
            'purge_at' => now()->subMinutes(5)
        ]);

        $url = route('profile.data-export.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertOk();

        $response->assertViewHas('exports');

        $response_exports = $response->data('exports');

        $this->assertCount(2, $response_exports->filter(function($value, $key) use($export, $expired_export) { return $value->id === $export->id || $value->id === $expired_export->id;}));
    }


    
    public function test_personal_export_is_created()
    {
        $disk = config('personal-export.disk');
        Storage::fake();
        Event::fake();
        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);
        $export_request = PersonalExport::requestNewExport($user);
        $this->createExportableDataForUser($user);

        $job = new PreparePersonalExportJob($export_request);

        $job->handle();

        Event::assertDispatched(PersonalExportReady::class);
        $export = PersonalExport::ofUser($user->id);
        $this->assertNotNull($export);
        Storage::disk($disk)->assertExists($export->name);

        $this->fail('TODO: assert if the zip file contains the expected files');
    }

    // function test_personal_export_notification_is_sent()
    // {

    // }
    
    // function test_personal_export_can_be_downloaded()
    // {

    // }
    

    
    // function test_expired_personal_export_are_purged()
    // {

    // }
    
    // function test_project_manager_personal_export_include_microsite()
    // {

    // }
}
