<?php

namespace Tests\Unit;

use KBox\File;
use Tests\TestCase;
use KBox\Events\FileDeleted;
use KBox\Events\FileRestored;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileEventsTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_deleted_event_fired_for_file_trash()
    {
        Event::fake();
        $file = factory(File::class)->create();
        $this->actingAs($file->user);

        $file->delete();

        Event::assertDispatched(FileDeleted::class, function ($e) use ($file) {
            return $e->file->id === $file->id && ! $e->forceDeleted && $e->user->is($file->user);
        });
    }
    
    public function test_deleted_event_fired_for_file_delete()
    {
        Event::fake();
        $file = factory(File::class)->create();
        $this->actingAs($file->user);

        $file->forceDelete();

        Event::assertDispatched(FileDeleted::class, function ($e) use ($file) {
            return $e->file->id === $file->id && $e->forceDeleted && $e->user->is($file->user);
        });
    }
    
    public function test_restored_event_fired_for_trashed_file()
    {
        Event::fake();
        $file = factory(File::class)->create();
        $file->delete();

        $file->restore();

        Event::assertDispatched(FileRestored::class, function ($e) use ($file) {
            return $e->file->id === $file->id && is_null($e->user);
        });
    }
    
    public function test_restored_event_get_current_authenticated_user()
    {
        Event::fake();
        $file = factory(File::class)->create();
        $this->actingAs($file->user);
        $file->delete();

        $file->restore();

        Event::assertDispatched(FileRestored::class, function ($e) use ($file) {
            return $e->file->id === $file->id && $e->user->is($file->user);
        });
    }
}
