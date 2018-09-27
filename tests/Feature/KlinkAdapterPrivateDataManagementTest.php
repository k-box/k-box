<?php

namespace Tests\Feature;

use Tests\TestCase;
use Klink\DmsAdapter\KlinkDocument;
use Klink\DmsAdapter\KlinkSearchResultItem;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use Klink\DmsAdapter\Exceptions\KlinkException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KlinkAdapterPrivateDataManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp()
    {
        parent::setUp();
        dump(getenv('DMS_CORE_ADDRESS'), config('dms.core.address'));
        if (empty(getenv('DMS_CORE_ADDRESS'))) {
            $this->markTestSkipped(
                'DMS_CORE_ADDRESS not configured for running integration tests.'
            );
        }
    }

    public function test_document_descriptor_is_added()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create();

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

        $this->assertInstanceOf(KlinkSearchResultItem::class, $response);

        $this->assertEquals($uuid, $response->uuid);
        $this->assertEquals('text/plain', $response->properties->mime_type);

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

    public function test_add_document_report_indexing_timeout()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'mime_type' => 'application/pdf'
        ]);
            
        $adapter = app('klinkadapter');
        
        $this->expectException(KlinkException::class);

        $response = $adapter->addDocument(new KlinkDocument($descriptor->toKlinkDocumentDescriptor(), null));
    }
}
