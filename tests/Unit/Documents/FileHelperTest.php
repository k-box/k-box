<?php

namespace Tests\Unit\Documents;

use Tests\TestCase;
use KBox\Documents\FileHelper;
use KBox\Documents\DocumentType;

class FileHelperTest extends TestCase
{
    public function test_hash_calculation()
    {
        $hash = FileHelper::hash(__DIR__.'/../../data/example.pdf');

        $this->assertEquals('f8ed72e302b0d0b5146191143d71c6e57d40af09d575331f2ff08be91cdff372ce3e4818a819042d010f7fada5d77e04f8bf08357d21c59069746faaf460cc8e', $hash);
    }

    public function test_type_recognition()
    {
        $file = __DIR__.'/../../data/example.pdf';

        $type = FileHelper::type($file);

        $this->assertEquals(['application/pdf', DocumentType::PDF_DOCUMENT], $type);
    }

    public function test_type_recognition_with_binary_file()
    {
        $file = __DIR__.'/../../data/example.bin';

        $type = FileHelper::type($file);

        $this->assertEquals(['application/octet-stream', DocumentType::BINARY], $type);
    }
    
    public function test_type_recognition_with_file_without_extension_is_considered_binary()
    {
        $file = __DIR__.'/../../data/example';

        $type = FileHelper::type($file);

        $this->assertEquals(['application/octet-stream', DocumentType::BINARY], $type);
    }

    public function test_mime_type_support_can_be_checked()
    {
        $supported = FileHelper::isMimeTypeSupported('application/pdf');

        $this->assertTrue($supported);
    }

    public function test_mime_type_support_of_normalizable_mime_type_can_be_checked()
    {
        $supported = FileHelper::isMimeTypeSupported('application/x-zip-compressed');

        $this->assertTrue($supported, 'application/x-zip-compressed is not supported');
    }

    public function test_extension_from_type_return_default_extension_if_document_type_is_not_provided()
    {
        $extension_normal = FileHelper::getExtensionFromType('image/tiff');
        $extension = FileHelper::getExtensionFromType('image/tiff', DocumentType::IMAGE);

        $this->assertEquals('tiff', $extension_normal);
        $this->assertEquals($extension_normal, $extension);
    }

    public function test_extension_from_type_return_discriminate_file_with_different_extension_but_same_mime_type()
    {
        $extension_normal = FileHelper::getExtensionFromType('image/tiff', DocumentType::IMAGE);
        $extension_geo = FileHelper::getExtensionFromType('image/tiff', DocumentType::GEODATA);

        $this->assertEquals('tiff', $extension_normal);
        $this->assertEquals('geotiff', $extension_geo);
    }

    public function test_zip_mime_type_variations_are_normalized()
    {
        $normalized = FileHelper::normalizeMimeType('application/x-zip-compressed');

        $this->assertEquals('application/zip', $normalized);
    }

    public function test_normalize_mime_type_do_not_touch_already_normalized_mime_types()
    {
        $normalized = FileHelper::normalizeMimeType('application/pdf');

        $this->assertEquals('application/pdf', $normalized);
    }
}
