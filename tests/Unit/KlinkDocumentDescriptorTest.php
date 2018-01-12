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
use KBox\Exceptions\UndefinedDefaultCopyrightUsageLicenseException;

class KlinkDocumentDescriptorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_private_document_descriptor_can_be_converted_to_ksearch_data()
    {
        $descriptor = factory('KBox\DocumentDescriptor')->make([
            // generating an author string that don't respect the format
            'authors' => 'Hello Author hello@author.com'
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
        $this->assertCount(1, $data->authors);
        $this->assertContainsOnlyInstancesOf(Author::class, $data->authors);
    }

    public function test_publishing_video_has_streaming_properties()
    {
        $streaming_url = 'https://streaming.service/play/123';

        $file = factory('KBox\File')->create([
            'path' => 'video.mp4',
            'mime_type' => 'video/mp4',
            ]);
            
        $descriptor = factory('KBox\DocumentDescriptor')->create([
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
        $descriptor = factory('KBox\DocumentDescriptor')->make([
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
        $descriptor = factory('KBox\DocumentDescriptor')->make([
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

        $descriptor = factory('KBox\DocumentDescriptor')->make([
            'copyright_usage' => null,
            'copyright_owner' => null
        ]);

        $data = $descriptor->toKlinkDocumentDescriptor()->toData();

        $this->assertEquals('PD', $data->copyright->usage->short);
        $this->assertEquals('Public domain', $data->copyright->usage->name);
        $this->assertEquals('', $data->copyright->usage->reference);

        $this->assertEquals('Not specified', $data->copyright->owner->name);
        $this->assertEquals('', $data->copyright->owner->email);
        $this->assertEquals('', $data->copyright->owner->website);
        $this->assertEquals('', $data->copyright->owner->address);
    }
    
    public function test_copyright_default_not_set_throws_exception()
    {
        Option::put(Option::COPYRIGHT_DEFAULT_LICENSE, null);

        $descriptor = factory('KBox\DocumentDescriptor')->make([
            'copyright_usage' => null,
            'copyright_owner' => null
        ]);

        $this->expectException(UndefinedDefaultCopyrightUsageLicenseException::class);

        $data = $descriptor->toKlinkDocumentDescriptor()->toData();
    }
}
