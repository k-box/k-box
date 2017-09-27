<?php

namespace Tests\Unit;

use KlinkDMS\File;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class FileTest extends TestCase
{
    public function test_path_attribute_is_relative()
    {
        Storage::fake('local');

        $file = (new File)->forceFill([
            'path' => Storage::disk('local')->path('something.pdf')
        ]);

        $this->assertNotEquals($file->path, $file->absolute_path);
        $this->assertEquals('something.pdf', $file->path);
        $this->assertEquals(Storage::disk('local')->path('something.pdf'), $file->absolute_path);
    }
    
    public function test_save_relative_path_attribute()
    {
        Storage::fake('local');

        $file = (new File)->forceFill([
            'path' => '2017/09/something.pdf'
        ]);

        $this->assertNotEquals($file->path, $file->absolute_path);
        $this->assertEquals('2017/09/something.pdf', $file->path);
        $this->assertEquals(Storage::disk('local')->path('2017/09/something.pdf'), $file->absolute_path);
    }

    public function test_file_delete_for_files_not_stored_in_uuid_folder()
    {
        Storage::fake('local');

        $storage = Storage::disk('local');

        $uuid = (new File)->resolveUuid();

        $storage->makeDirectory('2017/09/');
        $storage->put('2017/09/something.txt', 'The content');

        $file = (new File)->forceFill([
            'path' => '2017/09/something.txt',
            'uuid' => $uuid->getBytes()
        ]);
        
        Storage::disk('local')->assertExists($file->path);
        Storage::disk('local')->assertMissing("2017/09/{$uuid->toString()}/something.txt");

        $this->invokePrivateMethod($file, 'physicalDelete');
        
        Storage::disk('local')->assertExists('2017/09/');
        Storage::disk('local')->assertMissing("2017/09/something.txt");
    }
    
    public function test_file_delete_for_files_stored_in_uuid_folder()
    {
        Storage::fake('local');

        $storage = Storage::disk('local');

        $uuid = (new File)->resolveUuid();

        $storage->makeDirectory('2017/09/');
        $storage->put("2017/09/{$uuid->toString()}/something.txt", 'The content');
        $storage->put("2017/09/{$uuid->toString()}/something.png", 'The content');

        $file = (new File)->forceFill([
            'path' => "2017/09/{$uuid->toString()}/something.txt",
            'thumbnail_path' => "2017/09/{$uuid->toString()}/something.png",
            'uuid' => $uuid->getBytes()
        ]);
        
        Storage::disk('local')->assertExists($file->path);
        Storage::disk('local')->assertExists("2017/09/{$uuid->toString()}/something.txt");

        $this->invokePrivateMethod($file, 'physicalDelete');
        
        Storage::disk('local')->assertExists('2017/09/');
        Storage::disk('local')->assertMissing("2017/09/{$uuid->toString()}/");
    }
        
    public function test_thumbnail_path_attribute_is_relative()
    {
        Storage::fake('local');

        $file = (new File)->forceFill([
            'thumbnail_path' => Storage::disk('local')->path('something.png')
        ]);

        $this->assertNotEquals($file->thumbnail_path, $file->absolute_thumbnail_path);
        $this->assertEquals('something.png', $file->thumbnail_path);
        $this->assertEquals(Storage::disk('local')->path('something.png'), $file->absolute_thumbnail_path);
    }

    public function test_default_thumbnail_path_is_returned()
    {
        $path = public_path('images/document.png');

        Storage::fake('local');
        
        $file = (new File)->forceFill([
            'thumbnail_path' => $path
        ]);

        $this->assertEquals($path, $file->thumbnail_path);
        $this->assertEquals($path, $file->absolute_thumbnail_path);
    }

    public function test_default_fallback_thumbnail_is_not_deleted_from_storage()
    {
        $path = public_path('images/document.png');

        Storage::fake('local');
        
        $file = (new File)->forceFill([
            'thumbnail_path' => $path
        ]);

        $this->assertEquals($path, $file->thumbnail_path);

        $file->thumbnail_path = null;

        $this->assertNull($file->thumbnail_path);
        $this->assertTrue(file_exists($path));
    }

    public function test_old_thumbnail_path_is_deleted()
    {
        Storage::fake('local');

        $storage = Storage::disk('local');

        $storage->put("old_thumbnail.png", 'The content');
        $storage->put("new_thumbnail.png", 'The content');

        $file = (new File)->forceFill([
            'thumbnail_path' => 'old_thumbnail.png'
        ]);

        $this->assertEquals('old_thumbnail.png', $file->thumbnail_path);

        $file->thumbnail_path = 'new_thumbnail.png';

        $this->assertEquals('new_thumbnail.png', $file->thumbnail_path);
        Storage::disk('local')->assertMissing('old_thumbnail.png');
        
        $file->thumbnail_path = null;

        $this->assertNull($file->thumbnail_path);
        Storage::disk('local')->assertMissing('new_thumbnail.png');
    }
}
