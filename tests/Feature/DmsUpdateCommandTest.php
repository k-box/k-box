<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\DocumentDescriptor;
use KBox\Option;
use KBox\Capability;
use KBox\Institution;
use KBox\Publication;
use KBox\File;
use KBox\User;
use KBox\UserOption;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use KBox\Console\Commands\DmsUpdateCommand;
use Illuminate\Support\Facades\DB;

class DmsUpdateCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        \Schema::disableForeignKeyConstraints();
        DB::table('document_descriptors')->truncate();
        DB::table('files')->truncate();
        DB::table('options')->truncate();
    }

    /**
     * @test
     */
    public function verify_that_uuid_are_added_to_existing_document_descriptors()
    {
        $this->withKlinkAdapterMock();

        $docs = factory(\KBox\DocumentDescriptor::class, 11)->create(['uuid' => "00000000-0000-0000-0000-000000000000"]);
        $v3_docs = factory(\KBox\DocumentDescriptor::class)->create(['uuid' => "39613931-3436-3066-2d31-3533322d3466"]);

        $doc_ids = $docs->pluck('id')->toArray();
        
        // making sure that the install script thinks an update must be performed
        Option::create(['key' => 'c', 'value' => ''.time()]);

        $count_with_null_uuid = DocumentDescriptor::local()->withNullUuid()->count();

        $this->assertEquals($docs->count(), $count_with_null_uuid, 'Query cannot retrieve descriptors with null UUID');

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'generateDocumentsUuid');

        $this->assertEquals(12, $updated, 'Not all documents have been updated');
        
        $ret = DocumentDescriptor::local()->whereIn('id', array_merge($doc_ids, [$v3_docs->id]))->get();

        $this->assertEquals(12, $ret->count(), 'Not found the same documents originally created');

        $ret->each(function ($f) {
            $this->assertTrue(Uuid::isValid($f->uuid));
            $this->assertEquals(4, Uuid::fromString($f->uuid)->getVersion());
        });

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
        $institution = factory(\KBox\Institution::class)->create();
        $users = factory(\KBox\User::class, 3)->create([
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
        $docs = factory(\KBox\DocumentDescriptor::class, 3)->create(['is_public' => true]);
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

        $files = factory(\KBox\File::class, 11)->create(['uuid' => "00000000-0000-0000-0000-000000000000"]);
        $v3_files = factory(\KBox\File::class)->create(['uuid' => "39613931-3436-3066-2d31-3533322d3466"]);

        $file_ids = $files->pluck('id')->toArray();
        
        // making sure that the install script thinks an update must be performed
        Option::create(['key' => 'c', 'value' => ''.time()]);

        $count_with_null_uuid = File::withNullUuid()->count();

        $this->assertEquals($files->count(), $count_with_null_uuid, 'Query cannot retrieve files with null UUID');

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'generateFilesUuid');

        $this->assertEquals(12, $updated, 'Not all files have been updated');
        
        $ret = File::whereIn('id', array_merge($file_ids, [$v3_files->id]))->get();

        $this->assertEquals(12, $ret->count(), 'Not found the same files originally created');

        //second invokation of the same command

        $updated = $this->invokePrivateMethod($command, 'generateFilesUuid');

        $this->assertEquals(0, $updated, 'Some UUID has been regenerated');

        $ret->each(function ($f) {
            $this->assertTrue(Uuid::isValid($f->uuid));
            $this->assertEquals(4, Uuid::fromString($f->uuid)->getVersion());
        });
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
        
        $file = factory(\KBox\File::class)->create([
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
        
        $file = factory(\KBox\File::class)->create([
            'path' => Storage::disk('local')->path($path),
            'mime_type' => 'video/mp4'
        ]);

        Storage::disk('local')->assertExists("2017/11/video.mp4");

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'moveVideoFilesToUuidFolder');

        $this->assertEquals("2017/11/$file->uuid/$file->uuid.mp4", $file->fresh()->path);

        Storage::disk('local')->assertExists("2017/11/$file->uuid/$file->uuid.mp4");
    }
    
    public function test_video_files_already_in_uuid_folder_are_not_moved()
    {
        $this->withKlinkAdapterMock();
        
        Storage::fake('local');

        // making sure that the install script thinks an update must be performed
        Option::create(['key' => 'c', 'value' => ''.time()]);

        $uuid = "39613931-3436-3066-2d31-3533322d3466";
        
        $path = "2017/11/$uuid/$uuid.mp4";
        
        Storage::disk('local')->makeDirectory("2017/11/$uuid/");
        
        $file = factory(\KBox\File::class)->create([
            'path' => Storage::disk('local')->path($path),
            'mime_type' => 'video/mp4',
            'uuid' => $uuid
        ]);

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/video.mp4'))
        );

        Storage::disk('local')->assertExists($path);

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'moveVideoFilesToUuidFolder');

        $this->assertEquals("2017/11/$file->uuid/$file->uuid.mp4", $file->fresh()->path);

        Storage::disk('local')->assertExists("2017/11/$file->uuid/$file->uuid.mp4");
        Storage::disk('local')->assertMissing("2017/11/$file->uuid/$file->uuid/$file->uuid.mp4");
    }

    public function test_verify_that_terms_accepted_user_option_is_removed()
    {
        $this->withKlinkAdapterMock();

        $user = factory(User::class)->create();
        $user->options()->save(new UserOption(['key' => 'terms_accepted', 'value' => 'a-value']));

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'clearTermsAcceptedUserOption');

        $this->assertNull($user->getOption('terms_accepted'));
    }

    public function test_create_project_capability_is_added_only_when_upgrading()
    {
        $this->withKlinkAdapterMock();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER_LIMITED);
        });
        $user_to_touch = tap(factory(User::class)->create(), function ($u) {
            $caps = collect(Capability::$PROJECT_MANAGER)->reject(function ($value, $key) {
                return $value === Capability::CREATE_PROJECTS;
            });
            $u->addCapabilities($caps->values()->toArray());
        });

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'ensureCreateProjectsCapabilityIsSet');

        $this->assertNotNull(Option::findByKey('u_cap_cr_prj'));

        $this->assertTrue($user_to_touch->fresh()->can_all_capabilities(Capability::$PROJECT_MANAGER));
        $this->assertFalse($user->can_all_capabilities(Capability::$PROJECT_MANAGER));
    }

    public function test_create_project_capability_is_not_added_a_second_time()
    {
        $this->withKlinkAdapterMock();

        Option::create(['key' => 'u_cap_cr_prj', 'value' => ''.config('dms.version')]);

        $user = tap(factory(User::class)->create(), function ($u) {
            $caps = collect(Capability::$PROJECT_MANAGER)->reject(function ($value, $key) {
                return $value === Capability::CREATE_PROJECTS;
            });
            $u->addCapabilities($caps->values()->toArray());
        });

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'ensureCreateProjectsCapabilityIsSet');

        $this->assertFalse($user->fresh()->can_all_capabilities(Capability::$PROJECT_MANAGER));
    }

    public function test_deleted_capabilities_are_removed()
    {
        $this->withKlinkAdapterMock();

        Capability::firstOrCreate([ 'key' => Capability::MANAGE_USERS ]);
        Capability::firstOrCreate([ 'key' => Capability::MANAGE_LOG ]);
        Capability::firstOrCreate([ 'key' => Capability::MANAGE_BACKUP ]);
        Capability::firstOrCreate([ 'key' => Capability::IMPORT_DOCUMENTS ]);
        Capability::firstOrCreate([ 'key' => Capability::MANAGE_PEOPLE_GROUPS ]);
        Capability::firstOrCreate([ 'key' => Capability::MANAGE_PERSONAL_PEOPLE_GROUPS ]);

        $user = factory(User::class)->create();
        $user->addCapabilities([
            Capability::MANAGE_KBOX,
            Capability::MANAGE_USERS,
            Capability::MANAGE_LOG,
            Capability::MANAGE_BACKUP,
            Capability::IMPORT_DOCUMENTS,
            Capability::MANAGE_PEOPLE_GROUPS,
            Capability::MANAGE_PERSONAL_PEOPLE_GROUPS,
            Capability::SHARE_WITH_PRIVATE,
        ]);

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'removeDeletedCapabilities');

        $this->assertTrue($user->can_capability(Capability::MANAGE_KBOX));
        $this->assertFalse($user->can_capability(Capability::MANAGE_USERS));
        $this->assertFalse($user->can_capability(Capability::MANAGE_LOG));
        $this->assertFalse($user->can_capability(Capability::MANAGE_BACKUP));
        $this->assertFalse($user->can_capability(Capability::IMPORT_DOCUMENTS));
        $this->assertFalse($user->can_capability(Capability::MANAGE_PEOPLE_GROUPS));
        $this->assertFalse($user->can_capability(Capability::MANAGE_PERSONAL_PEOPLE_GROUPS));
        $this->assertFalse($user->can_capability(Capability::SHARE_WITH_PRIVATE));

        $this->assertNull(Capability::where('key', Capability::MANAGE_USERS)->first());
        $this->assertNull(Capability::where('key', Capability::MANAGE_LOG)->first());
        $this->assertNull(Capability::where('key', Capability::MANAGE_BACKUP)->first());
        $this->assertNull(Capability::where('key', Capability::IMPORT_DOCUMENTS)->first());
        $this->assertNull(Capability::where('key', Capability::MANAGE_PEOPLE_GROUPS)->first());
        $this->assertNull(Capability::where('key', Capability::MANAGE_PERSONAL_PEOPLE_GROUPS)->first());
        $this->assertNull(Capability::where('key', Capability::SHARE_WITH_PRIVATE)->first());
    }
}
