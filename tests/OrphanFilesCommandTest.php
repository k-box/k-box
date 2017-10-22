<?php

use KlinkDMS\File;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Console\Commands\OrphanFilesCommand;

use KlinkDMS\Traits\RunCommand;

/**
 * Test the {@see OrphanFilesCommand}
 */
class OrphanFilesCommandTest extends BrowserKitTestCase
{
    use DatabaseTransactions, RunCommand;

    public function setUp()
    {
        parent::setUp();

        \Schema::disableForeignKeyConstraints();
        \DB::table('document_descriptors')->truncate();
        \DB::table('files')->truncate();
    }

    /**
     * @return KlinkDMS\File the orphan to be identified
     */
    private function createSomeDocumentsAndFiles()
    {
        $user = $this->createAdminUser();

        // generate 3 document descriptors with file
        $docs = factory('KlinkDMS\DocumentDescriptor', 3)->create();

        // add a file revision to the last generated document
        $document3 = $docs->last();

        $template = base_path('tests/data/example.pdf');
        $destination = storage_path('documents/example-document.pdf');
        copy($template, $destination);

        $revision = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $destination,
            'hash' => hash_file('sha512', $destination),
            'revision_of' => $document3->file_id
        ]);

        $document3->file_id = $revision->id;
        $document3->save();

        // create an orphan file
        $orphan = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $destination,
            'hash' => hash_file('sha512', $destination)
        ]);
        
        // trash a document with its related file
        $to_be_trashed = factory('KlinkDMS\DocumentDescriptor')->create();
        $to_be_trashed->file->delete();
        $to_be_trashed->delete();

        // orphan file that is already trashed
        $deleted_orphan = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $destination,
            'hash' => hash_file('sha512', $destination)
        ]);
        $deleted_orphan->delete();

        return [$orphan, $deleted_orphan];
    }

    /**
     * Test the orphan command finds the corrects files
     */
    public function testOrphanListing()
    {
        list($orphan, $deleted_orphan) = $this->createSomeDocumentsAndFiles();

        $command = new OrphanFilesCommand();

        $res = $this->runArtisanCommand($command, []);

        $orphan = $orphan->fresh();

        $this->assertRegExp('/2 orphans found/', $res);
        $this->assertRegExp('/'.$orphan->id.'/', $res);
        $this->assertRegExp('/'.$deleted_orphan->id.'.*\(already trashed\)/', $res);
    }
    
    /**
     * Test the orphan command finds the corrects files
     */
    public function testOrphanListingWithPathOutput()
    {
        list($orphan, $deleted_orphan) = $this->createSomeDocumentsAndFiles();

        $command = new OrphanFilesCommand();

        $res = $this->runArtisanCommand($command, [
            '--file-paths' => true
        ]);

        $orphan = $orphan->fresh();

        $this->assertTrue(strpos($res, $orphan->path) !== false);
    }

    public function testOrphanDelete()
    {
        list($orphan, $deleted_orphan) = $this->createSomeDocumentsAndFiles();

        $command = new OrphanFilesCommand();

        $res = $this->runArtisanCommand($command, [
            '--delete' => true
        ]);

        $orphan = File::withTrashed()->find($orphan->id);

        $this->assertRegExp('/2 orphans found/', $res);
        $this->assertNotNull($orphan);
        $this->assertRegExp('/'.$orphan->id.'.*trashed/', $res);
        $this->assertTrue($orphan->trashed(), 'Orphan not trashed');
    }

    public function testOrphanPermanentDelete()
    {
        list($orphan, $deleted_orphan) = $this->createSomeDocumentsAndFiles();

        $command = new OrphanFilesCommand();

        $res = $this->runArtisanCommand($command, [
            '--force' => true
        ]);

        $this->assertNull(File::withTrashed()->find($orphan->id));
        $this->assertNull(File::withTrashed()->find($deleted_orphan->id));
        $this->assertRegExp('/2 orphans found/', $res);
        $this->assertRegExp('/'.$orphan->id.'.*deleted/', $res);
        $this->assertRegExp('/'.$deleted_orphan->id.'.*deleted/', $res);
    }
}
