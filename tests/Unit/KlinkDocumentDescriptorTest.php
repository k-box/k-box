<?php

namespace Tests\Unit;

use KBox\Option;
use Tests\TestCase;
use KSearchClient\Model\Data\Data;
use KSearchClient\Model\Data\Author;
use KSearchClient\Model\Data\Properties\Video;
use KSearchClient\Model\Data\Properties\Streaming;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KlinkDocumentDescriptorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_private_document_descriptor_can_be_converted_to_ksearch_data()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->make([
            // generating an author string that don't respect the format
            'authors' => 'Hello Author hello@author.com,Hello Author2 <hello@author2.com>'
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

        $this->assertNotNull($data->authors);
        $this->assertContainsOnlyInstancesOf(Author::class, $data->authors);
        $this->assertCount(2, $data->authors);

        $authors = $data->authors;
        $this->assertEquals('Hello Author', $authors[0]->name);
        $this->assertEquals('hello@author.com', $authors[0]->email);
        $this->assertEquals('Hello Author2', $authors[1]->name);
        $this->assertEquals('hello@author2.com', $authors[1]->email);
    }

    public function test_private_document_descriptor_is_anonymized()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->make();

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

        $this->assertEmpty($data->authors);
        $this->assertEquals(url('/'), $data->uploader->url);
        $this->assertEquals(sha1($descriptor->owner->id), $data->uploader->name);
    }

    public function test_document_descriptor_can_be_converted_to_public_descriptor()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create();

        $klink_descriptor = $descriptor->toKlinkDocumentDescriptor(true);

        $this->assertInstanceOf(KlinkDocumentDescriptor::class, $klink_descriptor);
        $this->assertEquals('public', $klink_descriptor->visibility());
        $this->assertEquals('public', $klink_descriptor->getVisibility());
        $this->assertEquals($descriptor->uuid, $klink_descriptor->uuid());
        $this->assertEquals($descriptor->uuid, $klink_descriptor->getKlinkId());

        $data = $klink_descriptor->toData();
        $this->assertInstanceOf(Data::class, $data);

        $this->assertEquals('document', $data->type);
        $this->assertEquals($descriptor->uuid, $data->uuid);
        $this->assertEquals($descriptor->hash, $data->hash);
        $this->assertEquals(route('documents.preview', ['id' => $descriptor->uuid]), $data->url);
        $this->assertEquals($descriptor->title, $data->properties->title);
        $this->assertEquals($descriptor->mime_type, $data->properties->mime_type);
        $this->assertEquals(route('documents.thumbnail', ['id' => $descriptor->uuid]), $data->properties->thumbnail);

        $this->assertEmpty($data->authors);
        $this->assertEquals(url('/'), $data->uploader->url);
        $this->assertEquals(sha1($descriptor->owner->id), $data->uploader->name);
    }

    public function test_publishing_video_has_streaming_properties()
    {
        $streaming_url = 'https://streaming.service/play/123';

        $file = factory(\KBox\File::class)->create([
            'path' => 'video.mp4',
            'mime_type' => 'video/mp4',
            ]);
            
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'file_id' => $file->id,
            'mime_type' => 'video/mp4',
            'is_public' => true
        ]);
        $descriptor->publications()->create([
            'pending' => true,
            'streaming_url' => $streaming_url
        ]);

        $data = $descriptor->fresh()->toKlinkDocumentDescriptor(true)->toData();

        $this->assertInstanceOf(Data::class, $data);

        $this->assertEquals('video', $data->type);
        $this->assertEquals($descriptor->uuid, $data->uuid);
        $this->assertEquals($descriptor->hash, $data->hash);
        $this->assertEquals($descriptor->mime_type, $data->properties->mime_type);

        $this->assertNotNull($data->properties->video);
        $this->assertInstanceOf(Video::class, $data->properties->video);
        
        $properties = $data->properties->video;
        
        $this->assertNotNull($properties->duration);
        $this->assertNotNull($properties->source);
        $this->assertNotNull($properties->streaming);
        $this->assertCount(1, $properties->streaming);
        $this->assertContainsOnlyInstancesOf(Streaming::class, $properties->streaming);
        $this->assertEquals($streaming_url, collect($properties->streaming)->first()->url);
    }

    public function test_copyright_is_added()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->make([
            'copyright_usage' => 'CC-BY-4.0',
            'copyright_owner' => collect([
                'name' => 'copyright owner',
                'website' => 'https://website.com',
                'email' => 'test@test.com',
                'address' => 'address field'
            ])
        ]);

        $data = $descriptor->toKlinkDocumentDescriptor()->toData();

        $this->assertEquals('CC-BY-4.0', $data->copyright->usage->short);
        $this->assertEquals('Creative Commons Attribution 4.0', $data->copyright->usage->name);
        $this->assertEquals('http://creativecommons.org/licenses/by/4.0/legalcode', $data->copyright->usage->reference);

        $this->assertEquals('copyright owner', $data->copyright->owner->name);
        $this->assertEquals('test@test.com', $data->copyright->owner->email);
        $this->assertEquals('https://website.com', $data->copyright->owner->website);
        $this->assertEquals('address field', $data->copyright->owner->address);
    }
    
    public function test_copyright_partial_owner_is_supported()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->make([
            'copyright_usage' => 'CC-BY-4.0',
            'copyright_owner' => collect([
                'name' => 'copyright owner',
            ])
        ]);

        $data = $descriptor->toKlinkDocumentDescriptor()->toData();

        $this->assertEquals('CC-BY-4.0', $data->copyright->usage->short);
        $this->assertEquals('Creative Commons Attribution 4.0', $data->copyright->usage->name);
        $this->assertEquals('http://creativecommons.org/licenses/by/4.0/legalcode', $data->copyright->usage->reference);

        $this->assertEquals('copyright owner', $data->copyright->owner->name);
        $this->assertEquals('', $data->copyright->owner->email);
        $this->assertEquals('', $data->copyright->owner->website);
        $this->assertEquals('', $data->copyright->owner->address);
    }

    public function test_copyright_default_is_used()
    {
        Option::put(Option::COPYRIGHT_DEFAULT_LICENSE, 'PD');

        $descriptor = factory(\KBox\DocumentDescriptor::class)->make([
            'copyright_usage' => null,
            'copyright_owner' => null
        ]);

        $data = $descriptor->toKlinkDocumentDescriptor()->toData();

        $this->assertEquals('PD', $data->copyright->usage->short);
        $this->assertNotEmpty($data->copyright->usage->name);
        $this->assertEquals('', $data->copyright->usage->reference);

        $this->assertEquals('-', $data->copyright->owner->name);
        $this->assertEquals('', $data->copyright->owner->email);
        $this->assertEquals('', $data->copyright->owner->website);
        $this->assertEquals('', $data->copyright->owner->address);
    }

    public function test_copyright_fallback_is_used()
    {
        Option::put(Option::COPYRIGHT_DEFAULT_LICENSE, null);

        $descriptor = factory(\KBox\DocumentDescriptor::class)->make([
            'copyright_usage' => null,
            'copyright_owner' => null
        ]);

        $data = $descriptor->toKlinkDocumentDescriptor()->toData();

        $this->assertEquals('UNK', $data->copyright->usage->short);
        $this->assertNotEmpty($data->copyright->usage->name);
        $this->assertEquals('', $data->copyright->usage->reference);

        $this->assertEquals('-', $data->copyright->owner->name);
        $this->assertEquals('', $data->copyright->owner->email);
        $this->assertEquals('', $data->copyright->owner->website);
        $this->assertEquals('', $data->copyright->owner->address);
    }

    public function test_mime_type_charset_is_sanitized()
    {
        $descriptor = factory(\KBox\DocumentDescriptor::class)->make([
            'mime_type' => 'text/html; charset UTF-8',
        ]);

        $data = $descriptor->toKlinkDocumentDescriptor()->toData();

        $this->assertEquals('text/html', $data->properties->mime_type);
    }
}
