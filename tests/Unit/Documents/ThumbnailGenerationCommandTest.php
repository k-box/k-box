<?php

namespace Tests\Unit\Documents;

use Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\File;
use KBox\DocumentDescriptor;
use KBox\Documents\Facades\Files;
use KBox\Console\Commands\ThumbnailGenerationCommand;

class ThumbnailGenerationCommandTest extends TestCase
{
    use DatabaseTransactions;
    
    public function testThumbnailGenerationConsole()
    {
        $real_path = base_path('tests/data/project-avatar.png');
        list($mime, $documentType) = Files::recognize($real_path);
        $file = factory(File::class)->create([
            'name' => basename($real_path),
            'hash' => Files::hash($real_path),
            'path' => $real_path,
            'mime_type' => $mime,
            'size' => filesize($real_path),
        ]);
        
        $document = factory(DocumentDescriptor::class)->create([
            'owner_id' => $file->user_id,
            'file_id' => $file->id,
        ]);
        
        $exitCode = Artisan::call('thumbnail:generate', [
            'documents' => [ $document->id ]
        ]);

        $output = Artisan::output();
        $this->assertEquals(0, $exitCode);
        
        $this->assertMatchesRegularExpression('/Generating thumbnails/', $output);
        $this->assertMatchesRegularExpression('/1 document/', $output);
        $this->assertMatchesRegularExpression('/100/', $output);
    }

    public function test_command_handles_non_existing_document()
    {
        $command = new ThumbnailGenerationCommand();
        
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $exitCode = Artisan::call('thumbnail:generate', [
            'documents' => ['89999999']
        ]);
    }
}
