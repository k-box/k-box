<?php

namespace Tests\Unit;

use Tests\TestCase;
use Klink\DmsAdapter\KlinkDocument;
use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;
use Klink\DmsAdapter\KlinkDocumentDescriptor;

class FakeKlinkAdapterTest extends TestCase
{
    public function test_add_document_increases_indexing_counter()
    {
        $adapter = new FakeKlinkAdapter();

        $document = new KlinkDocument(KlinkDocumentDescriptor::make($descriptor = factory('KlinkDMS\DocumentDescriptor')->make(), 'private'), 'document content');

        $adapter->addDocument($document);

        $adapter->assertDocumentIndexed($document->getDescriptor()->uuid());
    }
    
    public function test_update_document_increases_indexing_counter()
    {
        $adapter = new FakeKlinkAdapter();

        $document = new KlinkDocument(KlinkDocumentDescriptor::make($descriptor = factory('KlinkDMS\DocumentDescriptor')->make(), 'private'), 'document content');
        
        $adapter->addDocument($document);

        $adapter->updateDocument($document);

        $adapter->assertDocumentIndexed($document->getDescriptor()->uuid(), 2);
    }
    
    public function test_remove_document_increases_removed_counter()
    {
        $adapter = new FakeKlinkAdapter();
        
        $document = new KlinkDocument(KlinkDocumentDescriptor::make($descriptor = factory('KlinkDMS\DocumentDescriptor')->make(), 'private'), 'document content');
        
        $adapter->addDocument($document);

        $adapter->removeDocument($document->getDescriptor());

        $adapter->assertDocumentRemoved($document->getDescriptor()->uuid(), $document->getDescriptor()->visibility());
    }
    
    public function test_added_document_can_be_retrieved()
    {
        $adapter = new FakeKlinkAdapter();
        
        $document = new KlinkDocument(KlinkDocumentDescriptor::make($descriptor = factory('KlinkDMS\DocumentDescriptor')->make(), 'private'), 'document content');

        $adapter->addDocument($document);
        
        $retrieved_document = $adapter->getDocument($document->getDescriptor()->uuid(), $document->getDescriptor()->visibility());

        $this->assertNotNull($retrieved_document);
    }
}
