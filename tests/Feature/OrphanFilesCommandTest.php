<?php

namespace Tests\Feature;

use KBox\File;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use KBox\Console\Commands\OrphanFilesCommand;

use Illuminate\Support\Facades\Schema;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\User;

/**
 * Test the {@see OrphanFilesCommand}
 */
class OrphanFilesCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();
        DB::table('document_descriptors')->truncate();
        DB::table('files')->truncate();
    }

    /**
     * @return KBox\File the orphan to be identified
     */
    private function createSomeDocumentsAndFiles()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        // generate 3 document descriptors with file
        $docs = factory(DocumentDescriptor::class, 3)->create();

        // add a file revision to the last generated document
        $document3 = $docs->last();

        $template = base_path('tests/data/example.pdf');
        $destination = storage_path('documents/example-document.pdf');
        copy($template, $destination);

        $revision = factory(File::class)->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $destination,
            'hash' => hash_file('sha512', $destination),
            'revision_of' => $document3->file_id
        ]);

        $document3->file_id = $revision->id;
        $document3->save();

        // create an orphan file
        $orphan = factory(File::class)->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $destination,
            'hash' => hash_file('sha512', $destination)
        ]);
        
        // trash a document with its related file
        $to_be_trashed = factory(DocumentDescriptor::class)->create();
        $to_be_trashed->file->delete();
        $to_be_trashed->delete();

        // orphan file that is already trashed
        $deleted_orphan = factory(File::class)->create([
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

        $this->artisan('files:orphans')
            ->expectsOutput("2 orphans found")
            ->expectsOutput($orphan->name." (file_id: {$orphan->id}) ")
            ->expectsOutput($deleted_orphan->name." (file_id: {$deleted_orphan->id}) (already trashed)")
            ->assertExitCode(0);
    }
    
    /**
     * Test the orphan command finds the corrects files
     */
    public function testOrphanListingWithPathOutput()
    {
        list($orphan, $deleted_orphan) = $this->createSomeDocumentsAndFiles();

        $this->artisan('files:orphans', [
                '--file-paths' => true
            ])
            ->expectsOutput($orphan->path)
            ->expectsOutput($deleted_orphan->path)
            ->assertExitCode(0);
    }

    public function testOrphanDelete()
    {
        list($orphan, $deleted_orphan) = $this->createSomeDocumentsAndFiles();

        $this->artisan('files:orphans', [
                '--delete' => true
            ])
            ->expectsOutput("2 orphans found")
            ->expectsOutput($orphan->name." (file_id: {$orphan->id}) trashed")
            ->expectsOutput($deleted_orphan->name." (file_id: {$deleted_orphan->id}) (already trashed)")
            ->assertExitCode(0);

        $orphan = File::withTrashed()->find($orphan->id);

        $this->assertNotNull($orphan);
        $this->assertTrue($orphan->trashed(), 'Orphan not trashed');
    }

    public function testOrphanPermanentDelete()
    {
        list($orphan, $deleted_orphan) = $this->createSomeDocumentsAndFiles();

        $this->artisan('files:orphans', [
                '--force' => true
            ])
            ->expectsOutput("2 orphans found")
            ->expectsOutput($orphan->name." (file_id: {$orphan->id}) deleted")
            ->expectsOutput($deleted_orphan->name." (file_id: {$deleted_orphan->id}) deleted")
            ->assertExitCode(0);

        $this->assertNull(File::withTrashed()->find($orphan->id));
        $this->assertNull(File::withTrashed()->find($deleted_orphan->id));
    }
}
