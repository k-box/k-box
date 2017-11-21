<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Option;
use KlinkDMS\Institution;
use KlinkDMS\Publication;
use KlinkDMS\File;
use Illuminate\Support\Facades\Storage;
use KlinkDMS\Console\Commands\DmsUpdateCommand;

class DmsUpdateCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        \Schema::disableForeignKeyConstraints();
        \DB::table('document_descriptors')->truncate();
        \DB::table('files')->truncate();
        \DB::table('options')->truncate();
    }

    /**
     * @test
     */
    public function verify_that_uuid_are_added_to_existing_document_descriptors()
    {
        $this->withKlinkAdapterMock();

        $docs = factory('KlinkDMS\DocumentDescriptor', 3)->create(['uuid' => "00000000-0000-0000-0000-000000000000"]);

        $doc_ids = $docs->pluck('id')->toArray();
        
        // making sure that the install script thinks an update must be performed
        Option::create(['key' => 'c', 'value' => ''.time()]);

        $count_with_null_uuid = DocumentDescriptor::local()->withNullUuid()->count();

        $this->assertEquals($docs->count(), $count_with_null_uuid, 'Query cannot retrieve descriptors with null UUID');

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'generateDocumentsUuid');

        $this->assertEquals(3, $updated, 'Not all documents have been updated');
        
        $ret = DocumentDescriptor::local()->whereIn('id', $doc_ids)->get(['id', 'uuid']);

        $this->assertEquals(3, $ret->count(), 'Not found the same documents originally created');

        //second invokation of the same command

        $updated = $this->invokePrivateMethod($command, 'generateDocumentsUuid');

        $this->assertEquals(0, $updated, 'Some UUID has been regenerated');
    }

    public function test_default_institution_is_created()
    {
        $this->withKlinkAdapterFake();
        
        $current = Institution::current();

        if (! is_null($current)) {
            $current->delete();
        }

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'createDefaultInstitution');

        $this->assertNotNull(Institution::current());
    }

    public function test_user_affiliation_is_moved_to_organization()
    {
        $institution = factory('KlinkDMS\Institution')->create();
        $users = factory('KlinkDMS\User', 3)->create([
            'institution_id' => $institution->id
        ]);

        $user_ids = $users->pluck('id')->toArray();

        $command = new DmsUpdateCommand();
        
        $updated = $this->invokePrivateMethod($command, 'updateUserOrganizationAttributes');

        $this->assertEquals($users->count(), $updated);
    }

    public function test_old_publications_are_migrated_to_the_publications_table()
    {
        Publication::all()->each->delete();
        $docs = factory('KlinkDMS\DocumentDescriptor', 3)->create(['is_public' => true]);
        $ids = $docs->pluck('id');

        $command = new DmsUpdateCommand();
        
        $updated = $this->invokePrivateMethod($command, 'updatePublications');

        $this->assertEquals($docs->count(), $updated);

        $publications = Publication::published()->whereIn('descriptor_id', $ids->toArray())->get();

        $this->assertEquals($docs->count(), $publications->count());

        $updated = $this->invokePrivateMethod($command, 'updatePublications');
        
        $this->assertEquals(0, $updated);
    }

    public function test_that_files_uuid_are_generated()
    {
        $this->withKlinkAdapterMock();

        $files = factory('KlinkDMS\File', 3)->create(['uuid' => "00000000-0000-0000-0000-000000000000"]);

        $file_ids = $files->pluck('id')->toArray();
        
        // making sure that the install script thinks an update must be performed
        Option::create(['key' => 'c', 'value' => ''.time()]);

        $count_with_null_uuid = File::withNullUuid()->count();

        $this->assertEquals($files->count(), $count_with_null_uuid, 'Query cannot retrieve files with null UUID');

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'generateFilesUuid');

        $this->assertEquals(3, $updated, 'Not all files have been updated');
        
        $ret = File::whereIn('id', $file_ids)->get(['id', 'uuid']);

        $this->assertEquals(3, $ret->count(), 'Not found the same files originally created');

        //second invokation of the same command

        $updated = $this->invokePrivateMethod($command, 'generateFilesUuid');

        $this->assertEquals(0, $updated, 'Some UUID has been regenerated');
    }

    public function test_video_files_are_moved_to_uuid_folder()
    {
        $this->withKlinkAdapterMock();
        
        Storage::fake('local');

        // making sure that the install script thinks an update must be performed
        Option::create(['key' => 'c', 'value' => ''.time()]);

        $path = '2017/11/video.mp4';
        
        Storage::disk('local')->makeDirectory('2017/11/');

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );
        
        $file = factory('KlinkDMS\File')->create([
            'path' => $path,
            'mime_type' => 'video/mp4'
        ]);

        Storage::disk('local')->assertExists("2017/11/video.mp4");

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'moveVideoFilesToUuidFolder');

        $this->assertEquals("2017/11/$file->uuid/$file->uuid.mp4", $file->fresh()->path);

        Storage::disk('local')->assertExists("2017/11/$file->uuid/$file->uuid.mp4");
    }
    
    public function test_video_files_with_absolute_path_are_moved_to_uuid_folder()
    {
        $this->withKlinkAdapterMock();
        
        Storage::fake('local');

        // making sure that the install script thinks an update must be performed
        Option::create(['key' => 'c', 'value' => ''.time()]);

        $path = '2017/11/video.mp4';
        
        Storage::disk('local')->makeDirectory('2017/11/');

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );
        
        $file = factory('KlinkDMS\File')->create([
            'path' => Storage::disk('local')->path($path),
            'mime_type' => 'video/mp4'
        ]);

        Storage::disk('local')->assertExists("2017/11/video.mp4");

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'moveVideoFilesToUuidFolder');

        $this->assertEquals("2017/11/$file->uuid/$file->uuid.mp4", $file->fresh()->path);

        Storage::disk('local')->assertExists("2017/11/$file->uuid/$file->uuid.mp4");
    }
}
