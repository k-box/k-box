<?php

namespace Tests\Unit;

use Tests\TestCase;
use KSearchClient\Model\Data\Data;
use Klink\DmsAdapter\KlinkDocumentDescriptor;

class KlinkDocumentDescriptorTest extends TestCase
{
    public function test_private_document_descriptor_can_be_converted_to_ksearch_data()
    {
        $descriptor = factory('KlinkDMS\DocumentDescriptor')->make([
            'authors' => 'Hello Author <hello@author.com>, Second Author <second@author.com>'
        ]);

        $klink_descriptor = $descriptor->toKlinkDocumentDescriptor();

        $this->assertInstanceOf(KlinkDocumentDescriptor::class, $klink_descriptor);
        $this->assertEquals('private', $klink_descriptor->visibility());
        $this->assertEquals('private', $klink_descriptor->getVisibility());
        $this->assertEquals($descriptor->uuid, $klink_descriptor->uuid());
        $this->assertEquals($descriptor->uuid, $klink_descriptor->getKlinkId());

        $data = $klink_descriptor->toData();
        $this->assertInstanceOf(Data::class, $data);

        $this->assertEquals('document', $data->type);
        $this->assertEquals($descriptor->uuid, $data->uuid);
        $this->assertEquals($descriptor->hash, $data->hash);
        $this->assertRegExp('/http(.*)\/files\/(.*)\?t=(.*)/', $data->url);
        $this->assertStringStartsWith('http:', $data->url);
        $this->assertEquals($descriptor->title, $data->properties->title);
        $this->assertEquals($descriptor->mime_type, $data->properties->mime_type);
        $this->assertEquals($descriptor->thumbnail_uri, $data->properties->thumbnail);
    }
}
