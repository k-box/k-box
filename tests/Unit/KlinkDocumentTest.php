<?php

namespace Tests\Unit;

use Tests\TestCase;
use Klink\DmsAdapter\KlinkDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KlinkDocumentTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_url_to_private_file_is_generated()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create();

        $file = $descriptor->file;
        $document = new KlinkDocument($descriptor->toKlinkDocumentDescriptor(), 'file content');

        $data = $document->getDescriptor()->toData();

        $this->assertRegExp('/http(.*)\/files\/(.*)\?t=(.*)/', $data->url);
        $this->assertStringStartsWith('http:', $data->url);
    }
    
    public function test_url_to_private_file_uses_app_internal_url()
    {
        config([
            'app.internal_url' => 'http://docker.for.win.localhost:8000/'
        ]);

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create();

        $file = $descriptor->file;
        $document = new KlinkDocument($descriptor->toKlinkDocumentDescriptor(), 'file content');

        $data = $document->getDescriptor()->toData();

        $this->assertRegExp('/http(.*)\/files\/(.*)\?t=(.*)/', $data->url);
        $this->assertStringStartsWith('http://docker.for.win.localhost:8000/', $data->url);
    }
    
    public function test_url_to_public_file_is_generated()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'document_uri' => 'https://some.location/1'
        ]);

        $file = $descriptor->file;
        $document = new KlinkDocument($descriptor->toKlinkDocumentDescriptor(true), 'file content');

        $data = $document->getDescriptor()->toData();
        
        $this->assertNotEquals('https://some.location/1', $data->url);
        $this->assertEquals(route('documents.preview', $descriptor->uuid), $data->url);
    }
    
    public function test_document_data_returns_null_for_supported_data()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'mime_type' => 'application/pdf'
        ]);
        $file = $descriptor->file;

        $file->mime_type = 'application/pdf';
        $file->save();
        
        $document = new KlinkDocument($descriptor->toKlinkDocumentDescriptor(), 'file content');

        $this->assertNull($document->getDocumentData());
    }

    public function test_document_data_returns_alternate_string_for_unsupported_file()
    {
        Storage::fake('local');
        $storage = Storage::disk('local');

        $filename = 'something.png';

        $storage->put($filename, $this->generateImageFileContent());
        
        $hash = hash_file('sha512', $storage->path($filename));

        $file = factory(\KBox\File::class)->create([
            'name' => $filename,
            'hash' => $hash,
            'path' => $filename,
            'mime_type' => 'image/png',
            'size' => $storage->size($filename),
        ]);
        
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'hash' => $hash,
            'mime_type' => 'image/png',
            'file_id' => $file->id,
        ]);

        $document = new KlinkDocument($descriptor->toKlinkDocumentDescriptor(), $file->name);

        $this->assertEquals($file->name, $document->getDocumentData());
    }

    public function test_document_data_returns_file_content()
    {
        Storage::fake('local');
        $storage = Storage::disk('local');

        $filename = 'something.txt';

        $storage->put($filename, 'this is the text content');

        $hash = hash_file('sha512', $storage->path($filename));
        
        $file = factory(\KBox\File::class)->create([
            'name' => $filename,
            'hash' => $hash,
            'path' => $filename,
            'mime_type' => 'text/plain',
            'size' => $storage->size($filename),
        ]);
        
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'hash' => $hash,
            'mime_type' => 'text/plain',
            'file_id' => $file->id,
        ]);

        $document = new KlinkDocument($descriptor->toKlinkDocumentDescriptor(), $file->absolute_path);

        $this->assertEquals('this is the text content', $document->getDocumentData());
    }

    private function generateImageFileContent()
    {
        ob_start();
        
        imagepng(imagecreatetruecolor(10, 10));

        return ob_get_clean();
    }
}
