<?php

namespace Tests\Feature;

use Tests\TestCase;
use KSearchClient\Model\Data\Data;
use Klink\DmsAdapter\KlinkDocument;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KlinkAdapterPrivateDataManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp()
    {
        if (empty(getenv('DMS_CORE_ADDRESS'))) {
            $this->markTestSkipped(
              'DMS_CORE_ADDRESS not configured for running integration tests.'
            );
        }

        parent::setUp();
    }

    public function test_document_descriptor_is_added()
    {
        $descriptor = factory('KlinkDMS\DocumentDescriptor')->create();

        $adapter = app('klinkadapter');

        $response = $adapter->addDocument(new KlinkDocument($descriptor->toKlinkDocumentDescriptor(), 'file content'));

        $this->assertInstanceOf(KlinkDocumentDescriptor::class, $response);

        $this->assertEquals($descriptor->uuid, $response->uuid());

        return $descriptor->uuid;
    }
    
    /**
     * @depends test_document_descriptor_is_added
     */
    public function test_document_descriptor_is_retrieved($uuid)
    {
        $adapter = app('klinkadapter');
        
        $response = $adapter->getDocument($uuid);

        $this->assertInstanceOf(Data::class, $response);

        $this->assertEquals($uuid, $response->uuid);
        $this->assertEquals('application/pdf', $response->properties->mime_type);

        return $uuid;
    }
    
    /**
     * @depends test_document_descriptor_is_retrieved
     */
    public function test_document_descriptor_is_deleted($uuid)
    {
        $adapter = app('klinkadapter');
        
        $response = $adapter->removeDocumentById($uuid);

        $this->assertTrue($response);

        return $uuid;
    }
}
