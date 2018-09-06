<?php

namespace Tests\Unit\Documents;

use Tests\TestCase;
use InvalidArgumentException;
use KBox\Documents\DocumentType;
use KBox\Documents\Facades\Files;
use KBox\Documents\TypeIdentifier;
use KBox\Documents\TypeIdentification;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Services\FileService;
use Klink\DmsAdapter\Traits\SwapInstance;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileServiceTest extends TestCase
{
    use DatabaseTransactions, SwapInstance;

    public function test_identifier_registration_throws_if_non_existing_class_is_passed()
    {
        try {
            Files::register('ateam/head', DocumentType::NOTE, 'head', 'Class');

            $this->fail("An InvalidArgumentException was expected for the identifier class parameter");
        } catch (InvalidArgumentException $ex) {
            $this->assertContains("Class", $ex->getMessage());
            $this->assertContains("TypeIdentifier", $ex->getMessage());
        }
    }

    public function test_identifier_registration_throws_if_wrong_class_is_passed()
    {
        try {
            Files::register('ateam/head', DocumentType::NOTE, 'head', Files::class);

            $this->fail("An InvalidArgumentException was expected for the identifier class parameter");
        } catch (InvalidArgumentException $ex) {
            $this->assertContains(Files::class, $ex->getMessage());
            $this->assertContains("TypeIdentifier", $ex->getMessage());
        }
    }

    public function test_identifier_registration_throws_if_wrong_document_type_is_passed()
    {
        try {
            Files::register('ateam/head', 'DocumentType::NOTE', 'head', 'Class');

            $this->fail("An InvalidArgumentException was expected for the document type parameter");
        } catch (InvalidArgumentException $ex) {
            $this->assertContains('DocumentType::NOTE', $ex->getMessage());
            $this->assertContains("document type", $ex->getMessage());
        }
    }
    
    public function test_identifier_can_be_registered()
    {
        $this->swap(FileService::class, new FileService());

        Files::register('ateam/head', DocumentType::NOTE, 'head', TestingMimeTypeIdentifier::class);
        
        $identifiers = Files::identifiers();

        $this->assertEquals([[
              "accept" => "*",
              "priority" => 10,
              "mime" => "ateam/head",
              "doc" => DocumentType::NOTE,
              "extension" => "head",
              "identifier" => TestingMimeTypeIdentifier::class
          ]], $identifiers);
        $this->assertEquals('head', Files::extensionFromType('ateam/head', DocumentType::NOTE));
    }
    
    public function test_identifier_can_be_registered_once()
    {
        $this->swap(FileService::class, new FileService());

        $configured_identifiers = Files::identifiers();

        Files::register('ateam/head', DocumentType::NOTE, 'head', TestingMimeTypeIdentifier::class);
        Files::register('ateam/head', DocumentType::NOTE, 'head', TestingMimeTypeIdentifier::class);
        
        $identifiers = Files::identifiers();

        $this->assertEquals([[
            "accept" => "*",
            "priority" => 10,
            "mime" => "ateam/head",
            "doc" => DocumentType::NOTE,
            "extension" => "head",
            "identifier" => TestingMimeTypeIdentifier::class
        ]], $identifiers);
    }

    public function test_identifier_usage()
    {
        Storage::fake('local');

        $path = 'example.head';

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/example.txt'))
        );

        Files::register("ateam/head", DocumentType::NOTE, "head", TestingMimeTypeIdentifier::class);

        $type = Files::recognize(Storage::disk('local')->path($path));

        $this->assertEquals(['ateam/head', DocumentType::NOTE], $type);
    }

    public function test_identifier_usage_with_multiple_identifiers_for_same_type()
    {
        Storage::fake('local');

        $path = 'example.head';

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/example.txt'))
        );

        Files::register("ateam/head", DocumentType::NOTE, "head", TestingAllTypeIdentifier::class);
        Files::register("ateam/head", DocumentType::NOTE, "head", TestingMimeTypeIdentifier::class);

        $type = Files::recognize(Storage::disk('local')->path($path));

        $this->assertEquals(['ateam/head', DocumentType::NOTE], $type);
    }

    public function test_identifier_usage_with_multiple_identifiers_with_same_priority_for_same_type()
    {
        Storage::fake('local');

        $path = 'example.head';

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/example.txt'))
        );

        Files::register("ateam/face", DocumentType::NOTE, "head", TestingFourthAllTypeIdentifier::class);
        Files::register("ateam/face", DocumentType::NOTE, "head", TestingThirdAllTypeIdentifier::class);
        Files::register("ateam/head", DocumentType::NOTE, "head", TestingSecondAllTypeIdentifier::class);
        Files::register("ateam/head", DocumentType::NOTE, "head", TestingAllTypeIdentifier::class);

        $type = Files::recognize(Storage::disk('local')->path($path));

        $this->assertEquals(['ateam/head', DocumentType::NOTE], $type);
    }
    
    public function test_identifier_with_multiple_accept_values_is_supported()
    {
        Storage::fake('local');

        $path = 'example.txt';

        Storage::disk('local')->put(
            $path,
            file_get_contents(base_path('tests/data/example.txt'))
        );

        Files::register("ateam/head", DocumentType::NOTE, "head", TestingMultipleAcceptedTypeIdentifier::class);

        $type = Files::recognize($path);

        $this->assertEquals(['ateam/head', DocumentType::NOTE], $type);
    }
    
    public function test_registered_extension_is_used_when_converting_mime_type_to_extension()
    {
        Files::register("ateam/head", DocumentType::NOTE, "head", TestingMultipleAcceptedTypeIdentifier::class);

        $extension = Files::extensionFromType("ateam/head", DocumentType::NOTE);

        $this->assertEquals("head", $extension);
    }
}

class TestingAllTypeIdentifier extends TypeIdentifier
{
    public $accept = "*";

    public $priority = 1;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        return new TypeIdentification('ateam/head', DocumentType::NOTE);
    }
}

class TestingSecondAllTypeIdentifier extends TypeIdentifier
{
    public $accept = "*";

    public $priority = 1;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        return new TypeIdentification('ateam/head', DocumentType::NOTE);
    }
}

class TestingThirdAllTypeIdentifier extends TypeIdentifier
{
    public $accept = "*";

    public $priority = 1;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        return new TypeIdentification('ateam/face', DocumentType::NOTE);
    }
}

class TestingFourthAllTypeIdentifier extends TypeIdentifier
{
    public $accept = "*";

    public $priority = 1;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        return new TypeIdentification('ateam/t', DocumentType::NOTE);
    }
}

class TestingMimeTypeIdentifier extends TypeIdentifier
{
    public $accept = "*";

    public $priority = 10;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        return new TypeIdentification('ateam/head', DocumentType::NOTE);
    }
}

class TestingMultipleAcceptedTypeIdentifier extends TypeIdentifier
{
    public $accept = ["text/plain", "ateam/head"];

    public $priority = 10;

    public function identify(string $path, TypeIdentification $default) : TypeIdentification
    {
        return new TypeIdentification('ateam/head', DocumentType::NOTE);
    }
}
