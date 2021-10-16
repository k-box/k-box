<?php

namespace Tests\Feature;

use KBox\File;
use Tests\TestCase;

class RefreshFileMimeTypeCommandTest extends TestCase
{
    public function test_file_mime_type_updated()
    {
        $file = File::factory()->create([
            'mime_type' => 'audio/mpeg',
            'path' => base_path('tests/data/audio.mp3')
        ]);

        $this->artisan('file:fix-type')
            ->expectsOutput("Mime type updated for 1 file.")
            ->expectsOutput("Execute php artisan documents:check-latest-version to ensure changes are propagated.")
            ->assertExitCode(0);

        $updatedFile = $file->fresh();
        
        $this->assertEquals('audio/mp3', $updatedFile->mime_type);
        $this->assertEquals('audio', $updatedFile->document_type);
    }
    
    public function test_file_mime_type_update_when_single_file_specified()
    {
        $file = File::factory()->create([
            'mime_type' => 'application/json',
        ]);

        $this->artisan('file:fix-type', [
                'file' => $file->getKey()
            ])
            ->expectsOutput("Mime type updated for the specified file.")
            ->expectsOutput("Execute php artisan documents:check-latest-version {$file->document_id} to ensure changes are propagated.")
            ->assertExitCode(0);

        $updatedFile = $file->fresh();

        $this->assertEquals('text/plain', $updatedFile->mime_type);
        $this->assertEquals('text-document', $updatedFile->document_type);
    }
    
    public function test_file_mime_type_update_not_required()
    {
        $file = File::factory()->create([
            'mime_type' => 'text/plain',
        ]);

        $this->artisan('file:fix-type')
            ->expectsOutput("Mime type updated for 0 file.")
            ->assertExitCode(0);
    }
}
