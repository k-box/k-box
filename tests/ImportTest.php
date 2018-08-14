<?php

use Tests\BrowserKitTestCase;
use KBox\Import;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\User;
use KBox\File;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\Project;
use KBox\Jobs\ImportCommand;

use KBox\Console\Commands\DmsImportCommand;

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
    
    /**
     * Tests the dms:import command for importing local storage folder with Project creation from root folders option.
     * This test attempt to index all the files
     */
    public function testImportFromSameFolderViaArtisanCommandWithProjectCreationAndLocalOption_Integration()
    {
        $this->withKlinkAdapterFake();
        
        $user = $this->createAdminUser();
        
        $command = new DmsImportCommand(app('KBox\Documents\Services\DocumentsService'));

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
        $this->markTestSkipped(
            'This test fails on Travis, but not on Gitlab CI. Need investigation.'
        );

        $this->withKlinkAdapterFake();
        
        $user = $this->createAdminUser();
        
        $command = new DmsImportCommand(app('KBox\Documents\Services\DocumentsService'));

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
     * @expectedException \KBox\Exceptions\ForbiddenException
     * @expectedExceptionMessage The user must be at least a project administrator
     */
    public function testImportFromSameFolderViaArtisanCommandWithWrongUserParameter()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);
        
        $command = new DmsImportCommand(app('KBox\Documents\Services\DocumentsService'));
        
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
        $command = new DmsImportCommand(app('KBox\Documents\Services\DocumentsService'));
        
        $res = $this->runArtisanCommand($command, [
            'folder' => './non-existing-folder/',
            '--local' => null,
        ]);
    }

    public function test_import_job_refuses_to_process_url_imports()
    {
        $this->withKlinkAdapterFake();
        
        $url = 'https://klink.asia/fail.pdf';

        $save_path = Config::get('dms.upload_folder').DIRECTORY_SEPARATOR.md5($url);
        
        $user = $this->createAdminUser();
        
        $file = factory(\KBox\File::class)->create([
            'mime_type' => '',
            'size' => 0,
            'path' => $save_path,
            'user_id' => $user->id,
            'original_uri' => $url
        ]);
        
        $import = factory(\KBox\Import::class)->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'is_remote' => true,
            'status' => Import::STATUS_QUEUED,
            'message' => Import::MESSAGE_QUEUED,
        ]);
        
        $job = new ImportCommand($user, $import);

        $job->handle(app('KBox\Documents\Services\DocumentsService'));

        $import = $import->fresh();

        $this->assertEquals(Import::STATUS_ERROR, $import->status);
        $this->assertEquals('Import from URL are not supported', $import->message);
    }

    protected function runCommand($command, $input = [], $output = null)
    {
        if (is_null($output)) {
            $output = new Symfony\Component\Console\Output\NullOutput;
        }
        
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), $output);
    }
}
