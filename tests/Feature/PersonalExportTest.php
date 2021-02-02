<?php

namespace Tests\Feature;

use KBox\User;
use KBox\File;
use KBox\Group;
use ZipArchive;
use KBox\Starred;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use KBox\PersonalExport;
use KBox\DocumentDescriptor;
use KBox\Documents\Facades\Files;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use KBox\Events\PersonalExportCreated;
use KBox\Jobs\PreparePersonalExportJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use KBox\Notifications\PersonalExportReadyNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalExportTest extends TestCase
{
    use DatabaseTransactions;

    private function createExportableDataForUser($user)
    {
        $file = factory(File::class)->create([
            'user_id' => $user->id,
            'original_uri' => '',
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
            // 'is_private' => true
        ]);

        return [
            $file,
            $doc,
            $starred,
            $project,
            $collection
        ];
    }

    private function getZipContentList($path)
    {
        $entries = [];
        $za = new ZipArchive;
        $za->open($path);
        for ($i=0; $i < $za->numFiles; $i++) {
            $entry = $za->statIndex($i);
            $entries[] = $entry['name'];
        }
        $za->close();
        return $entries;
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

        $this->assertCount(2, $response_exports->filter(function ($value, $key) use ($export, $expired_export) {
            return $value->id === $export->id || $value->id === $expired_export->id;
        }));
    }

    public function test_exports_can_be_downloaded()
    {
        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
            'generated_at' => now()->subMinute(),
            'purge_at' => now()->addMinutes(5)
        ]);

        $url = route('profile.data-export.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertOk();

        $response->assertViewHas('exports');

        $response->assertSee(route('profile.data-export.download', ['export' => $export->name]), false);
    }
    
    public function test_personal_export_is_created()
    {
        $disk = config('personal-export.disk');
        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);
        list($file) = $this->createExportableDataForUser($user);
        
        Storage::fake($disk);
        Event::fake();

        $export_request = PersonalExport::requestNewExport($user);

        $job = new PreparePersonalExportJob($export_request);

        $job->handle();

        Event::assertDispatched(PersonalExportCreated::class);
        $export = PersonalExport::ofUser($user->id)->first();
        $this->assertNotNull($export);
        Storage::disk($disk)->assertExists($export->name);

        $zipEntries = $this->getZipContentList(Storage::disk($disk)->path($export->name));

        $this->assertContains('readme.txt', $zipEntries);
        $this->assertContains('user.json', $zipEntries);
        $this->assertContains('collections.json', $zipEntries);
        $this->assertContains('publications.json', $zipEntries);
        $this->assertContains('stars.json', $zipEntries);
        $this->assertContains('documents.json', $zipEntries);
        $this->assertContains('projects.json', $zipEntries);
        
        $extension = Files::extensionFromType($file->mime_type);
        
        $this->assertContains("{$file->uuid}.$extension", $zipEntries);
    }

    public function test_personal_export_notification_is_sent()
    {
        Notification::fake();

        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
        ]);

        event(new PersonalExportCreated($export));

        Notification::assertSentTo(
            $user,
            PersonalExportReadyNotification::class,
            function ($notification, $channels) use ($export) {
                return $notification->export->id === $export->id;
            }
        );
    }

    public function test_expired_personal_export_are_purged()
    {
        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
        ]);
        $expired_export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
            'purge_at' => now()->subMinutes(5)
        ]);

        $this->artisan('data-export:purge')
            ->expectsOutput('Expired personal exports purged.')
            ->assertExitCode(0);

        $this->assertEquals(1, PersonalExport::ofUser($user)->count());
    }
    
    public function test_personal_export_can_be_downloaded()
    {
        $disk = config('personal-export.disk');
        Storage::fake($disk);
        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        Storage::disk($disk)->put('export.zip', 'content');

        $pending_export = factory(PersonalExport::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subMinute(),
            'purge_at' => now()->addMinutes(15),
            'name' => 'export.zip'
        ]);
        
        $url = route('profile.data-export.download', ['export' => 'export.zip']);

        $response = $this->actingAs($user)->get($url);

        $response->assertHeader('Content-Disposition', 'attachment; filename="export.zip"');
        $response->assertHeader('Content-Type', 'application/zip');
        $response->assertHeader('Content-Length', Storage::disk($disk)->size('export.zip'));
        
        $content = $response->streamedContent();

        $this->assertEquals('content', $content);
    }
}
