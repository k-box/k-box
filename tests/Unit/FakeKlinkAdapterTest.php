<?php

namespace Tests\Unit;

use Tests\TestCase;
use Klink\DmsAdapter\KlinkDocument;
use Klink\DmsAdapter\KlinkSearchRequest;
use Klink\DmsAdapter\KlinkSearchResults;
use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FakeKlinkAdapterTest extends TestCase
{
    use DatabaseTransactions; // because the model factories depends on the database for some fields

    public function test_add_document_increases_indexing_counter()
    {
        $adapter = new FakeKlinkAdapter();

        $document = new KlinkDocument(KlinkDocumentDescriptor::make($descriptor = factory(\KBox\DocumentDescriptor::class)->make(), 'private'), 'document content');

        $adapter->addDocument($document);

        $adapter->assertDocumentIndexed($document->getDescriptor()->uuid());
    }
    
    public function test_update_document_increases_indexing_counter()
    {
        $adapter = new FakeKlinkAdapter();

        $document = new KlinkDocument(KlinkDocumentDescriptor::make($descriptor = factory(\KBox\DocumentDescriptor::class)->make(), 'private'), 'document content');
        
        $adapter->addDocument($document);

        $adapter->updateDocument($document);

        $adapter->assertDocumentIndexed($document->getDescriptor()->uuid(), 2);
    }
    
    public function test_remove_document_increases_removed_counter()
    {
        $adapter = new FakeKlinkAdapter();
        
        $document = new KlinkDocument(KlinkDocumentDescriptor::make($descriptor = factory(\KBox\DocumentDescriptor::class)->make(), 'private'), 'document content');
        
        $adapter->addDocument($document);

        $adapter->removeDocument($document->getDescriptor());

        $adapter->assertDocumentRemoved($document->getDescriptor()->uuid(), $document->getDescriptor()->visibility());
    }
    
    public function test_added_document_can_be_retrieved()
    {
        $adapter = new FakeKlinkAdapter();
        
        $document = new KlinkDocument(KlinkDocumentDescriptor::make($descriptor = factory(\KBox\DocumentDescriptor::class)->make(), 'private'), 'document content');

        $adapter->addDocument($document);
        
        $retrieved_document = $adapter->getDocument($document->getDescriptor()->uuid(), $document->getDescriptor()->visibility());

        $this->assertNotNull($retrieved_document);
    }

    public function test_search_can_be_faked()
    {
        $adapter = new FakeKlinkAdapter();
        
        // prepare the request
        $searchRequest = KlinkSearchRequest::build('hello', 'private', 1, 12, [], []);

        // prepare some fake results
        $adapter->setSearchResults('private', KlinkSearchResults::fake($searchRequest, []));
        
        // invoke the search
        $results = $adapter->search($searchRequest);

        $this->assertEquals(0, $results->getTotalResults());

        $adapter->assertSearched($searchRequest);
    }

    public function test_facets_can_be_faked()
    {
        $adapter = new FakeKlinkAdapter();

        // prepare the request
        $searchRequest = KlinkSearchRequest::build('*', 'private', 1, 1, [], []);

        // prepare some fake results
        $adapter->setSearchResults('private', KlinkSearchResults::fake($searchRequest, []));
        
        // invoke the search
        $result_facets = $adapter->facets([], 'private');

        $this->assertEmpty($result_facets);

        $adapter->assertSearched($searchRequest);
    }
}
