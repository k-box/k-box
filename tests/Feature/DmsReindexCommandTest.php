<?php

namespace Tests\Feature;

use Tests\TestCase;

use KBox\User;
use KBox\DocumentDescriptor;

use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test the DMSReindexCommand
*/
class DmsReindexCommandTest extends TestCase
{
    use DatabaseTransactions;

    // function that might be useful

    private function createDocuments($quantity = 5)
    {
        $docs = factory(DocumentDescriptor::class, $quantity)->create([
            'is_public' => false,
            'language' => null
        ]);
        
        return $docs;
    }

    // real test methods

    /**
     * Test the reindex of a single document and checks that the updated_at info
     * is not automatically updated on save
     */
    public function testReindexButNotUpdatedAtUpdate()
    {
        $adapter = $this->withKlinkAdapterFake();

        $document_count = 3;

        $docs = $this->createDocuments($document_count);

        $first_doc = $docs->first();

        $updated_at = $first_doc->updated_at;

        $plucked = $docs->pluck('id')->toArray();

        $this->artisan('dms:reindex', [
                'documents' => $plucked,
            ])
            ->expectsOutput("Reindexing $document_count documents...")
            ->assertExitCode(0);

        $docs->each(function ($doc) use ($adapter) {
            $adapter->assertDocumentIndexed($doc->uuid);
        });

        $after = $first_doc->fresh()->updated_at;

        $this->assertEquals($updated_at, $after, 'different updated_at values');
    }

    /**
     * Test the reindex with local document id as option
     */
    public function testReindexByLocalDocumentId()
    {
        $adapter = $this->withKlinkAdapterFake();

        $document_count = 3;

        $docs = $this->createDocuments($document_count);

        $plucked = $docs->pluck('local_document_id');
            
        $this->artisan('dms:reindex', [
                'documents' => $plucked->toArray(),
                '--klink-id' => true
            ])
            ->expectsOutput("Reindexing ".$document_count." documents...")
            ->assertExitCode(0);

        $docs->each(function ($doc) use ($adapter) {
            $adapter->assertDocumentIndexed($doc->uuid);
        });
    }

    /**
     * Test the reindex with local document id option activated on an empty list of documents
     */
    public function testReindexByLocalDocumentIdWithEmptyArgument()
    {
        $this->artisan('dms:reindex', [
                '--klink-id' => true
            ])
            ->expectsOutput('Option --klink-id can only be used if argument list is not empty.')
            ->assertExitCode(127);
    }

    public function testReindexByUserWithArguments()
    {
        $this->artisan('dms:reindex', [
                'documents' => [ '10' ],
                '--users' => 44
            ])
            ->expectsOutput('Documents cannot be specified in conjunction with option --user.')
            ->assertExitCode(127);
    }

    /**
     * Test the reindex for all documents of a user
     */
    public function testReindexByUser()
    {
        $adapter = $this->withKlinkAdapterFake();

        $document_count = 3;

        $docs = $this->createDocuments($document_count);

        $first = $docs->shift();

        $user = $first->owner_id;

        $this->artisan('dms:reindex', [
                '--users' => $user
            ])
            ->expectsOutput("Reindexing 1 documents...")
            ->assertExitCode(0);

        $adapter->assertDocumentIndexed($first->uuid);
    }

    /**
     * Test the reindex of a sub-set of the whole documents
     */
    public function testReindexWithTakeAndSkip()
    {
        $adapter = $this->withKlinkAdapterFake();

        $document_count = 6;
        
        $expected_count = 3;

        $skip = 2;

        $take = 3;

        $docs = $this->createDocuments($document_count);
        $all_docs = $docs->pluck('id')->toArray();

        $expected_docs = $docs->splice($skip, $take);

        $expected_docs_ids = $expected_docs->pluck('id')->toArray();

        $this->artisan('dms:reindex', [
                'documents' => $all_docs,
                '--skip' => $skip,
                '--take' =>  $take
            ])
            ->expectsOutput("Reindexing $expected_count documents...")
            ->expectsOutput("Take $take, Skip $skip")
            ->assertExitCode(0);

        $expected_docs->each(function ($item) use ($expected_docs_ids, $adapter) {
            $item = $item->fresh();

            // Check if the status attribute has been populated for the expected updated documents
            $this->assertContains($item->id, $expected_docs_ids);
            $this->assertEquals($item->status, DocumentDescriptor::STATUS_INDEXED);

            $adapter->assertDocumentIndexed($item->uuid);
        });
    }

    public function testReindexWithTakeAndSkipWithStrings()
    {
        $adapter = $this->withKlinkAdapterFake();

        $document_count = 6;
        
        $expected_count = 3;

        $skip = 2;

        $take = 3;

        $docs = $this->createDocuments($document_count);
        $all_docs = $docs->pluck('id')->toArray();

        $expected_docs = $docs->splice($skip, $take);

        $expected_docs_ids = $expected_docs->pluck('id')->toArray();

        $this->artisan('dms:reindex', [
                'documents' => $all_docs,
                '--skip' => "".$skip,
                '--take' =>  "".$take
            ])
            ->expectsOutput("Reindexing $expected_count documents...")
            ->expectsOutput("Take $take, Skip $skip")
            ->assertExitCode(0);

        $expected_docs->each(function ($item) use ($expected_docs_ids, $adapter) {
            $item = $item->fresh();

            // Check if the status attribute has been populated for the expected updated documents
            $this->assertContains($item->id, $expected_docs_ids);
            $this->assertEquals(DocumentDescriptor::STATUS_INDEXED, $item->status);

            $adapter->assertDocumentIndexed($item->uuid);
        });
    }

    /**
     * Test the reindex with skip array value
     */
    public function testReindexWithTakeAndSkipWithArrays()
    {
        $adapter = $this->withKlinkAdapterFake();

        $document_count = 6;
        
        $expected_count = 3;

        $skip = 2;

        $take = 3;

        $docs = $this->createDocuments($document_count);

        $expected_docs = $docs->splice($skip, $take);

        $expected_docs_ids = $expected_docs->pluck('id')->toArray();

        $this->artisan('dms:reindex', [
                '--skip' => [$skip],
                '--take' => [$take]
            ])
            ->expectsOutput('Take must be a positive integer. Minimum value 1')
            ->assertExitCode(127);
    }

    public function testReindexNegativeSkipArgument()
    {
        $this->artisan('dms:reindex', [
                '--skip' => -4
            ])
            ->expectsOutput('Skip must be a positive integer or zero.')
            ->assertExitCode(127);
    }

    public function testReindexZeroTakeArgument()
    {
        $this->artisan('dms:reindex', [
                '--take' => 0
            ])
            ->expectsOutput('Take must be a positive integer. Minimum value 1')
            ->assertExitCode(127);
    }

    public function testReindexNegativeTakeArgument()
    {
        $this->artisan('dms:reindex', [
                '--take' => -4
            ])
            ->expectsOutput('Take must be a positive integer. Minimum value 1')
            ->assertExitCode(127);
    }

    /**
     * Test the reindex of the documents passed as argument with the offset/limit combination
     */
    public function testReindexByIdWithTake()
    {
        $adapter = $this->withKlinkAdapterFake();

        $document_count = 6;
        
        $expected_count = 3;

        $skip = 2;

        $take = 3;

        $docs = $this->createDocuments($document_count);

        $plucked = $docs->pluck('id');

        $expected_docs = $docs->splice($skip, $take);

        $this->artisan('dms:reindex', [
                'documents' => $plucked->toArray(),
                '--skip' => $skip,
                '--take' =>  $take
            ])
            ->expectsOutput("Reindexing $expected_count documents...")
            ->expectsOutput("Take $take, Skip $skip")
            ->assertExitCode(0);
        
        $expected_docs->each(function ($item) use ($adapter) {
            $item = $item->fresh();

            // Check if the status attribute has been populated for the expected updated documents
            $this->assertEquals(DocumentDescriptor::STATUS_INDEXED, $item->status);

            $adapter->assertDocumentIndexed($item->uuid);
        });
    }
}
