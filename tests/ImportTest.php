<?php

use Tests\BrowserKitTestCase;
use KlinkDMS\Import;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Project;
use KlinkDMS\Jobs\ImportCommand;

use Illuminate\Foundation\Application;
use KlinkDMS\Console\Commands\DmsImportCommand;

class ImportTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function user_provider()
    {
        return [
            [Capability::$ADMIN, 200],
            [Capability::$DMS_MASTER, 403],
            [Capability::$PROJECT_MANAGER, 200],
            [Capability::$PARTNER, 403],
            [Capability::$GUEST, 403],
        ];
    }
    
    
    public function url_provider()
    {
        return [
            [ 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_RUS.pdf' ],
            [ 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_KYR-small.pdf' ],
            [ 'http://www.conservation.org/Pages/default.aspx' ],
            [ 'http://www.conservation.org/publications/Documents/CI_Ecosystem-based-Adaptation-South-Africa-Vulnerability-Assessment-Brochure.pdf' ],
            [ 'https://www.change.org/p/unfccc-united-nations-framework-convention-on-climate-change-ensure-that-the-impact-of-climate-change-on-mountain-peoples-and-ecosystems-is-fully-addressed-in-the-unfccc-cop21-new-climate-deal?source_location=petitions_share_skip' ],
            [ 'https://www.change.org/p/unfccc-united-nations-framework-convention-on-climate-change-ensure-that-the-impact-of-climate-change-on-mountain-peoples-and-ecosystems-is-fully-addressed-in-the-unfccc-cop21-new-climate-deal?source_location' ],
            [ 'http://klink.asia' ],
        ];
    }
    
    
    public function url_import_provider()
    {
        return [
            [ 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_RUS.pdf', 'application/pdf' ],
            [ 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_KYR-small.pdf', 'application/pdf' ],
            [ 'http://www.conservation.org/Pages/default.aspx', 'text/html' ],
            [ 'http://www.conservation.org/publications/Documents/CI_Ecosystem-based-Adaptation-South-Africa-Vulnerability-Assessment-Brochure.pdf', 'application/pdf' ],
            [ 'https://www.change.org/p/unfccc-united-nations-framework-convention-on-climate-change-ensure-that-the-impact-of-climate-change-on-mountain-peoples-and-ecosystems-is-fully-addressed-in-the-unfccc-cop21-new-climate-deal?source_location=petitions_share_skip', 'text/html' ],
            [ 'https://www.change.org/p/unfccc-united-nations-framework-convention-on-climate-change-ensure-that-the-impact-of-climate-change-on-mountain-peoples-and-ecosystems-is-fully-addressed-in-the-unfccc-cop21-new-climate-deal?source_location', 'text/html' ],
            [ 'http://klink.asia', 'text/html' ],
            [ 'http://www.iisd.org/sites/default/files/publications/mainstreaming-climate-change-toolkit-guidebook.pdf', 'application/pdf' ],
        ];
    }
    
    public function url_to_clean_provider()
    {
        return [
            [ '  http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_RUS.pdf#aiudhsuds ', 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_RUS.pdf' ],
            [ 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_KYR-small.pdf#help-me', 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_KYR-small.pdf' ],
            [ 'http://www.conservation.org/Pages/default.aspx ', 'http://www.conservation.org/Pages/default.aspx' ],
        ];
    }
    
    /**
     * Test the import page loading based on user capabilities
     *
     * @dataProvider user_provider
     * @return void
     */
    public function testImportPageLoading($caps, $expected_code)
    {
        $this->withKlinkAdapterFake();
        
        // $this->beginDatabaseTransaction();
        
        $user = $this->createUser($caps);
        
        $this->actingAs($user);
        
        $this->visit(route('documents.import'));
             
        if ($expected_code === 200) {
            $this->assertResponseOk();
            $this->see('Import');
            $this->seePageIs(route('documents.import'));
        } else {
            $view = $this->response->original;
            
            $this->assertEquals('errors.'.$expected_code, $view->name());
        }
    }
    
    
    /**
     * Test if the Import job is raised when creating an import
     *
     * @dataProvider url_provider
     */
    public function testImportControllerCreatesImportJob($url)
    {
        $this->withKlinkAdapterFake();
        
        $this->withoutMiddleware();
        
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        $this->json('POST', route('documents.import'), [
            'from' => 'remote',
            'remote_import' => $url
        ])->seeJson();
        
        $this->assertResponseOk();
        
        $stored_import = Import::fromUser($user->id)->first();
        $stored_import_count = Import::fromUser($user->id)->count();
        
        $this->assertEquals(1, $stored_import_count);
        $this->assertNotNull(1, $stored_import);
        $this->assertTrue(! ! $stored_import->is_remote, "Stored import not marked as remote");
        $this->assertEquals($url, $stored_import->file->original_uri);
    }
        
    /**
     * @dataProvider url_import_provider
     */
    public function testImportFromUrlJob($url, $mime_type)
    {
        $this->markTestSkipped(
            'Needs to be reimplemented.'
          );
          
        // create an ImportJob and run it
        $this->withKlinkAdapterFake();

        $uuid = (new \KlinkDMS\File)->resolveUuid()->toString();
        
        $save_path = date('Y').'/'.date('m').'/'.$uuid.'/'.md5($url).'.html';
        
        if (file_exists($save_path)) {
            unlink($save_path);
        }
        
        
        $user = $this->createAdminUser();
        
        $file = factory('KlinkDMS\File')->create([
            'mime_type' => '',
            'size' => 0,
            'uuid' => $uuid,
            'path' => $save_path,
            'user_id' => $user->id,
            'original_uri' => $url
        ]);
        
        $import = factory('KlinkDMS\Import')->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'is_remote' => true
        ]);

        dispatch(new ImportCommand($user, $import)); //make sure to have QUEUE_DRIVER=sync in testing.env
        
        
        $saved_import = Import::with('file')->findOrFail($import->id);
        
        
        $this->assertEquals(KlinkDMS\Import::STATUS_COMPLETED, $saved_import->status, 'Import not completed');
        
        $this->assertEquals(KlinkDMS\Import::MESSAGE_COMPLETED, $saved_import->status_message);
        
        $this->assertTrue(file_exists($saved_import->file->absolute_path), "File do not exists");
        
        $this->assertEquals($saved_import->bytes_expected, $saved_import->bytes_received, "Bytes expected and received are not equals");
        
        $this->assertNotEquals(md5($url), $saved_import->file->hash, "File hash not changed");
        
        $this->assertNotEquals(0, $saved_import->file->size, "File Size not changed");
        $this->assertEquals($saved_import->bytes_received, $saved_import->file->size, "File Size not equal to downloaded size");
        
        $this->assertContains($mime_type, $saved_import->file->mime_type, "Inferred mime type is different than what is expected");
    }
    
    
    public function testImportFailurePayloadStored()
    {
        $this->withKlinkAdapterFake();
        
        $url = 'https://klink.asia/fail.pdf';
        
        $save_path = Config::get('dms.upload_folder').DIRECTORY_SEPARATOR.md5($url);
        
        $user = $this->createAdminUser();
        
        $file = factory('KlinkDMS\File')->create([
            'mime_type' => '',
            'size' => 0,
            'path' => $save_path,
            'user_id' => $user->id,
            'original_uri' => $url
        ]);
        
        $import = factory('KlinkDMS\Import')->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'is_remote' => true
        ]);

        dispatch(new ImportCommand($user, $import)); //make sure to have QUEUE_DRIVER=sync in testing.env
        
        $saved_import = Import::findOrFail($import->id);
        
        $this->assertNotNull($saved_import->job_payload);
        $this->assertEquals(Import::STATUS_ERROR, $saved_import->status);
    }
    
    public function testDestroyImportWithCompletedStatus()
    {
        $this->withKlinkAdapterFake();
        
        $url = 'https://klink.asia/fail.pdf';
        
        $save_path = Config::get('dms.upload_folder').DIRECTORY_SEPARATOR.md5($url);
        
        $user = $this->createAdminUser();
        
        $file = factory('KlinkDMS\File')->create([
            'mime_type' => '',
            'size' => 0,
            'path' => $save_path,
            'user_id' => $user->id,
            'original_uri' => $url
        ]);
        
        $import = factory('KlinkDMS\Import')->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'is_remote' => true,
            'status' => Import::STATUS_COMPLETED,
            'message' => Import::MESSAGE_COMPLETED,
        ]);
        
        $this->actingAs($user);
        
        \Session::start(); // Start a session for the current test

        $this->json('DELETE', route('documents.import.destroy', [
                'id' => $import->id,
                '_token' => csrf_token()])
             );
        $this->seeJson([
            'status' => 'ok',
            'message' => trans('import.remove.removed_message', ['import' => $import->file->name])
        ]);
        
        $this->assertResponseOk();
    }
    
    public function testDestroyImportWithPendingStatus()
    {
        $this->withKlinkAdapterFake();
        
        $url = 'https://klink.asia/fail.pdf';
        
        $save_path = Config::get('dms.upload_folder').DIRECTORY_SEPARATOR.md5($url);
        
        $user = $this->createAdminUser();
        
        $file = factory('KlinkDMS\File')->create([
            'mime_type' => '',
            'size' => 0,
            'path' => $save_path,
            'user_id' => $user->id,
            'original_uri' => $url
        ]);
        
        $import = factory('KlinkDMS\Import')->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'is_remote' => true,
            'status' => Import::STATUS_DOWNLOADING,
            'message' => Import::MESSAGE_DOWNLOADING,
        ]);
        
        $this->actingAs($user);
        
        \Session::start(); // Start a session for the current test

        $this->json('DELETE', route('documents.import.destroy', [
                'id' => $import->id,
                '_token' => csrf_token()])
             );

        $this->seeJson([
            'status' => 'error',
            'error' => trans('import.remove.destroy_forbidden_status')
        ]);
        
        $this->assertResponseStatus(422);
    }
    
    public function testDestroyImportFromAnotherUser()
    {
        $this->withKlinkAdapterFake();
        
        $url = 'https://klink.asia/fail.pdf';
        
        $save_path = Config::get('dms.upload_folder').DIRECTORY_SEPARATOR.md5($url);
        
        $user = $this->createAdminUser();
        $user2 = $this->createUser(Capability::$PROJECT_MANAGER);
        
        $file = factory('KlinkDMS\File')->create([
            'mime_type' => '',
            'size' => 0,
            'path' => $save_path,
            'user_id' => $user->id,
            'original_uri' => $url
        ]);
        
        $import = factory('KlinkDMS\Import')->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'is_remote' => true,
            'status' => Import::STATUS_DOWNLOADING,
            'message' => Import::MESSAGE_DOWNLOADING,
        ]);
        
        $this->actingAs($user2);
        
        \Session::start(); // Start a session for the current test

        $this->json('DELETE', route('documents.import.destroy', [
                'id' => $import->id,
                '_token' => csrf_token()])
             );
        
        $this->seeJson([
            'status' => 'error',
            'error' => trans('import.remove.destroy_forbidden_user', ['import' => $import->file->name])
        ]);
             
        $this->assertResponseStatus(422);
    }
    
    
    public function testRetryImport()
    {
        $this->withKlinkAdapterFake();
        
        $url = 'https://klink.asia/fail.pdf';
        
        $save_path = Config::get('dms.upload_folder').DIRECTORY_SEPARATOR.md5($url);
        
        $user = $this->createAdminUser();
        
        $file = factory('KlinkDMS\File')->create([
            'mime_type' => '',
            'size' => 0,
            'path' => $save_path,
            'user_id' => $user->id,
            'original_uri' => $url
        ]);
        
        $import = factory('KlinkDMS\Import')->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'is_remote' => true
        ]);

        dispatch(new ImportCommand($user, $import)); //make sure to have QUEUE_DRIVER=sync in testing.env
        
        $saved_import = Import::findOrFail($import->id);
        
        $this->assertNotNull($saved_import->job_payload);
        $this->assertEquals(Import::STATUS_ERROR, $saved_import->status);
        
        
        $this->actingAs($user);
        
        \Session::start(); // Start a session for the current test

        $this->json('PUT', route('documents.import.update', [
                'id' => $import->id,
                '_token' => csrf_token()]), ['retry' => true]);
        
        $this->seeJson([
            'status' => 'ok',
            'message' => trans('import.retry.retry_completed_message', ['import' => $saved_import->file->name])
        ]);
             
        $this->assertResponseStatus(200);
    }
    
    /**
     * Tests the dms:import command for importing local storage folder with Project creation from root folders option.
     * This test attempt to index all the files
     */
    public function testImportFromSameFolderViaArtisanCommandWithProjectCreationAndLocalOption_Integration()
    {
        $this->withKlinkAdapterFake();
        
        $user = $this->createAdminUser();
        
        $command = new DmsImportCommand(app('Klink\DmsDocuments\DocumentsService'));

        $res = $this->runArtisanCommand($command, [
            'folder' => __DIR__.'/data/folder_for_import/',
            '--create-projects' => null,
            '--local' => null,
            '-u' => $user->id,
            ]);
        
        $expected_projects = [
            'folder1' => ['subfolder1'],
            'folder2' => ['subfolder2', 'subfolder3'],
        ];
        
        $expected_folders_path = [
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder1',
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder1'.DIRECTORY_SEPARATOR.'subfolder1',
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder2',
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder2'.DIRECTORY_SEPARATOR.'subfolder2',
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder2'.DIRECTORY_SEPARATOR.'subfolder3',
        ];
        
        $prj = null;
        
        $this->assertRegExp('/Gathering folder structure for (.*)folder_for_import/', $res);
        $this->assertRegExp('/File already exists/', $res);
        
        foreach ($expected_projects as $prj_name => $sub_collections) {
            
            // check for projects existence
            
            $prj = Project::where('name', $prj_name)->first();
            $this->assertNotNull($prj);
            $this->assertEquals($prj_name, $prj->name);
            $this->assertEquals($user->id, $prj->user_id);
            
            // check for collections existence
            $this->assertNotNull($prj->collection);
            $this->assertEquals($prj_name, $prj->collection->name);
            $this->assertFalse(! ! $prj->collection->is_private);
        }
        
        // Check that import infos are stored
        
        $this->assertEquals(5, File::where('user_id', $user->id)->where('is_folder', true)->count());
        $this->assertEquals(5, File::whereIn('path', $expected_folders_path)->count());
        $this->assertEquals(5, Import::where('user_id', $user->id)->count());
        
        $this->assertEquals(5, File::where('user_id', $user->id)->where('is_folder', true)->count());
        
        // in the test data we have 5 files, but in-subfolder-2.md and in-subfolder-3.md have the same content, so a Duplicated file conflict resolution is expected
        
        $this->assertEquals(4, File::where('user_id', $user->id)->where('mime_type', 'text/x-markdown')->count());
        $this->assertEquals(4, Import::where('user_id', $user->id)->where('status', Import::STATUS_COMPLETED)->count(), 'completed imports count');
    }
    
    /**
     * Tests the dms:import command for importing local storage folder with Project creation from root folders option.
     * This test attempt to index all the files
     */
    public function testImportFromSameFolderViaArtisanCommandWithProjectCreationAndLocalOption_Integration_WithConflictResolution()
    {
        $this->withKlinkAdapterFake();
        
        $user = $this->createAdminUser();
        
        $command = new DmsImportCommand(app('Klink\DmsDocuments\DocumentsService'));

        $res = $this->runArtisanCommand($command, [
            'folder' => __DIR__.'/data/folder_for_import/',
            '--create-projects' => null,
            '--local' => null,
            '--attempt-to-resolve-file-conflict' => null,
            '-u' => $user->id,
            ]);
        
        $expected_projects = [
            'folder1' => ['subfolder1'],
            'folder2' => ['subfolder2', 'subfolder3'],
        ];
        
        $expected_folders_path = [
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder1',
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder1'.DIRECTORY_SEPARATOR.'subfolder1',
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder2',
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder2'.DIRECTORY_SEPARATOR.'subfolder2',
            __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder2'.DIRECTORY_SEPARATOR.'subfolder3',
        ];
        
        $prj = null;
        
        $this->assertRegExp('/Gathering folder structure for (.*)folder_for_import/', $res);
        $this->assertRegExp('/File already exists/', $res);
        $this->assertRegExp('/Attempting to merge document descriptors/', $res);
        
        foreach ($expected_projects as $prj_name => $sub_collections) {
            
            // check for projects existence
            
            $prj = Project::where('name', $prj_name)->first();
            $this->assertNotNull($prj);
            $this->assertEquals($prj_name, $prj->name);
            $this->assertEquals($user->id, $prj->user_id);
            
            // check for collections existence
            $this->assertNotNull($prj->collection);
            $this->assertEquals($prj_name, $prj->collection->name);
            $this->assertFalse(! ! $prj->collection->is_private);
        }
        
        // Check that import infos are stored
        
        $this->assertEquals(5, File::where('user_id', $user->id)->where('is_folder', true)->count());
        $this->assertEquals(5, File::whereIn('path', $expected_folders_path)->count());
        $this->assertEquals(5, Import::where('user_id', $user->id)->count());
        
        $this->assertEquals(5, File::where('user_id', $user->id)->where('is_folder', true)->count());
        
        // in the test data we have 5 files, but in-subfolder-2.md and in-subfolder-3.md have the same content, so a Duplicated file conflict resolution is expected
        
        $this->assertEquals(4, File::where('user_id', $user->id)->where('mime_type', 'text/x-markdown')->count());
        $this->assertEquals(5, Import::where('user_id', $user->id)->where('status', Import::STATUS_COMPLETED)->count(), 'completed imports count');
        
        
        // Check if the file conflict resolution was completed correctly
        $f = File::where('path', __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'folder_for_import'.DIRECTORY_SEPARATOR.'folder2'.DIRECTORY_SEPARATOR.'subfolder2'.DIRECTORY_SEPARATOR.'in-subfolder-2.md')->first();
        $this->assertNotNull($f, 'Not able to find original file for conflict resolution');
        
        $descriptor = DocumentDescriptor::where('file_id', $f->id)->first();
        
        $this->assertNotNull($descriptor, 'Not able to find document descriptor for file that triggered conflict resolution procedure');
        $this->assertRegExp('/3/', $descriptor->abstract, 'Abstract not updated after conflict resolution');
        $this->assertEquals(2, $descriptor->groups()->count(), 'The document should be in two groups after conflict resolution');
    }
    
    /**
     * @expectedException \KlinkDMS\Exceptions\ForbiddenException
     * @expectedExceptionMessage The user must be at least a project administrator
     */
    public function testImportFromSameFolderViaArtisanCommandWithWrongUserParameter()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);
        
        $command = new DmsImportCommand(app('Klink\DmsDocuments\DocumentsService'));
        
        $res = $this->runArtisanCommand($command, [
            'folder' => __DIR__.'/data/folder_for_import/',
            '--create-projects' => null,
            '--local' => null,
            '-u' => $user->id,
        ]);
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The specified folder "./non-existing-folder/" is not a valid folder
     */
    public function testImportFromSameFolderViaArtisanCommandWithWrongFolder()
    {
        $command = new DmsImportCommand(app('Klink\DmsDocuments\DocumentsService'));
        
        $res = $this->runArtisanCommand($command, [
            'folder' => './non-existing-folder/',
            '--local' => null,
        ]);
    }

    /**
     * @dataProvider url_to_clean_provider
     */
    public function testImportUrlIsCleaned($url, $expected_url)
    {
        $preexisting_import = Import::all()->pluck('id');

        Queue::shouldReceive('push')->once()->with(\Mockery::type('KlinkDMS\Jobs\ImportCommand'));

        $user = $this->createAdminUser();

        $this->withKlinkAdapterFake();
        
        $save_path = Config::get('dms.upload_folder').DIRECTORY_SEPARATOR.md5($url);
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $service->importFromUrl($url, $user);

        $after_import = Import::all()->pluck('id');

        $diff = $after_import->diff($preexisting_import);

        $this->assertTrue($diff->count() === 1, 'Multiple imports generated');

        $import = Import::findOrFail($diff->first());

        $this->assertEquals($expected_url, $import->file->original_uri);
    }
    
    
    protected function runCommand($command, $input = [], $output = null)
    {
        if (is_null($output)) {
            $output = new Symfony\Component\Console\Output\NullOutput;
        }
        
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), $output);
    }
}
