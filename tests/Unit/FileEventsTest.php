<?php

namespace Tests\Unit;

use KBox\File;
use Tests\TestCase;
use KBox\Events\FileDeleted;
use KBox\Events\FileRestored;
use Illuminate\Support\Facades\Event;

class FileEventsTest extends TestCase
{
    public function test_deleted_event_fired_for_file_trash()
    {
        Event::fake();
        $file = File::factory()->create();
        $this->actingAs($file->user);

        $file->delete();

        Event::assertDispatched(FileDeleted::class, function ($e) use ($file) {
            return $e->file->id === $file->id && ! $e->forceDeleted && $e->user->is($file->user);
        });
    }
    
    public function test_deleted_event_fired_for_file_delete()
    {
        $file = File::factory()->create();

        Event::fake();

        $this->actingAs($file->user);

        $file->forceDelete();

        Event::assertDispatched(FileDeleted::class, function ($e) use ($file) {
            return $e->file->id === $file->id && $e->forceDeleted && $e->user->is($file->user);
        });
    }
    
    public function test_restored_event_fired_for_trashed_file()
    {
        $file = File::factory()->create();
        
        Event::fake();

        $file->delete();

        $file->restore();

        Event::assertDispatched(FileRestored::class, function ($e) use ($file) {
            return $e->file->id === $file->id && is_null($e->user);
        });
    }
    
    public function test_restored_event_get_current_authenticated_user()
    {
        $file = File::factory()->create();
        
        Event::fake();
        
        $this->actingAs($file->user);
        
        $file->delete();

        $file->restore();

        Event::assertDispatched(FileRestored::class, function ($e) use ($file) {
            return $e->file->id === $file->id && $e->user->is($file->user);
        });
    }
}
