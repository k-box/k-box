<?php

namespace Tests\Unit;

use KBox\File;
use Tests\TestCase;
use Carbon\Carbon;
use KBox\FileProperties;
use Illuminate\Support\Facades\Crypt;
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

    public function test_download_token_is_generated()
    {
        $uuid = (new File)->resolveUuid();

        $file = (new File)->forceFill([
            'name' => 'something.txt',
            'path' => '2017/09/something.txt',
            'uuid' => $uuid->getBytes()
        ]);

        $token = $file->generateDownloadToken();

        $plain_token = Crypt::decryptString($token);
        
        $components = explode('#', $plain_token);

        $this->assertCount(4, $components);

        $this->assertArraySubset([
            $file->uuid,
            $file->hash
        ], $components);

        $created_at = Carbon::createFromTimestamp($components[2]);
        $expire_at = Carbon::createFromTimestamp($components[3]);

        $this->assertTrue($created_at->isToday());
        $this->assertTrue($expire_at->isToday());
        $this->assertTrue($expire_at->gte($created_at));
        $this->assertTrue($created_at->eq($expire_at->subMinutes(5)));
    }

    public function test_download_token_with_custom_duration_is_generated()
    {
        $uuid = (new File)->resolveUuid();

        $file = (new File)->forceFill([
            'name' => 'something.txt',
            'hash' => 'abcdefgh',
            'path' => '2017/09/something.txt',
            'uuid' => $uuid->getBytes()
        ]);

        $token = $file->generateDownloadToken(10);

        $plain_token = Crypt::decryptString($token);

        $components = explode('#', $plain_token);

        $this->assertCount(4, $components);

        $this->assertArraySubset([
            $file->uuid,
            $file->hash
        ], $components);

        $created_at = Carbon::createFromTimestamp($components[2]);
        $expire_at = Carbon::createFromTimestamp($components[3]);

        $this->assertTrue($created_at->isToday());
        $this->assertTrue($expire_at->isToday());
        $this->assertTrue($expire_at->gte($created_at));
        $this->assertTrue($created_at->eq($expire_at->subMinutes(10)));
    }

    public function test_array_based_file_properties_are_saved()
    {
        $uuid = (new File)->resolveUuid();
        
        $file = (new File)->forceFill([
            'name' => 'something.txt',
            'hash' => 'abcdefgh',
            'path' => '2017/09/something.txt',
            'uuid' => $uuid->getBytes(),
            'properties' => ['author' => 'Jules', 'address' => ['street' => 'rue 1,1']]
        ]);

        $this->assertNotEmpty($file->properties);
        $this->assertInstanceOf(FileProperties::class, $file->properties);
        $this->assertEquals('Jules', $file->properties->author);
        $this->assertEquals('Jules', $file->properties->get('author'));
        $this->assertEquals('rue 1,1', $file->properties->{"address.street"});
        $this->assertEquals('rue 1,1', $file->properties->get('address.street'));
    }

    public function test_custom_file_properties_are_saved()
    {
        $uuid = (new File)->resolveUuid();

        $properties = new CustomFileProperties(['author' => 'Jules', 'address' => ['street' => 'rue 1,1']]);
        
        $file = (new File)->forceFill([
            'name' => 'something.txt',
            'hash' => 'abcdefgh',
            'path' => '2017/09/something.txt',
            'uuid' => $uuid->getBytes(),
            'properties' => $properties
        ]);

        $this->assertNotEmpty($file->properties);
        $this->assertInstanceOf(CustomFileProperties::class, $file->properties);
        $this->assertEquals('Jules', $file->properties->author);
        $this->assertEquals('Jules', $file->properties->get('author'));
        $this->assertEquals('rue 1,1', $file->properties->{"address.street"});
        $this->assertEquals('rue 1,1', $file->properties->get('address.street'));
    }

    public function test_custom_file_properties_can_be_extended()
    {
        $uuid = (new File)->resolveUuid();

        $properties = (new CustomFileProperties(['author' => 'Jules', 'address' => ['street' => 'rue 1,1']]))->merge(['phone' => '5050505']);
        
        $file = (new File)->forceFill([
            'name' => 'something.txt',
            'hash' => 'abcdefgh',
            'path' => '2017/09/something.txt',
            'uuid' => $uuid->getBytes(),
            'properties' => $properties
        ]);

        $this->assertNotEmpty($file->properties);
        $this->assertInstanceOf(CustomFileProperties::class, $file->properties);
        $this->assertEquals('Jules', $file->properties->author);
        $this->assertEquals('Jules', $file->properties->get('author'));
        $this->assertEquals('rue 1,1', $file->properties->{"address.street"});
        $this->assertEquals('rue 1,1', $file->properties->get('address.street'));
        $this->assertEquals('5050505', $file->properties->phone);
    }
}

class CustomFileProperties extends FileProperties
{
}
