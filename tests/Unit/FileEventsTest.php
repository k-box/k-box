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

        $file->delete();

        Event::assertDispatched(FileDeleted::class, function ($e) use ($file) {
            return $e->file->id === $file->id && ! $e->forceDeleted;
        });
    }
    
    public function test_deleted_event_fired_for_file_delete()
    {
        Event::fake();
        $file = factory(File::class)->create();

        $file->forceDelete();

        Event::assertDispatched(FileDeleted::class, function ($e) use ($file) {
            return $e->file->id === $file->id && $e->forceDeleted;
        });
    }
    
    public function test_restored_event_fired_for_trashed_file()
    {
        Event::fake();
        $file = factory(File::class)->create();
        $file->delete();

        $file->restore();

        Event::assertDispatched(FileRestored::class, function ($e) use ($file) {
            return $e->file->id === $file->id;
        });
    }
}
