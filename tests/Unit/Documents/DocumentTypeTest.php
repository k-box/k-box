<?php

namespace Tests\Unit\Documents;

use Tests\TestCase;
use KBox\Documents\DocumentType;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentTypeTest extends TestCase
{

    public function test_handles_character_encoding()
    {
        $docType = DocumentType::documentTypeFromMimeType('text/html;charset=utf8');
        $docTypeUsingFrom = DocumentType::from('text/html;charset=utf8');
        
        $this->assertEquals(DocumentType::WEB_PAGE, $docType);
        $this->assertEquals($docType, $docTypeUsingFrom);
    }
    
    public function test_handles_unknown_mime_type()
    {
        $docType = DocumentType::documentTypeFromMimeType('ateam/face');
        $docTypeUsingFrom = DocumentType::from('ateam/face');
        
        $this->assertEquals(DocumentType::BINARY, $docType);
        $this->assertEquals($docType, $docTypeUsingFrom);
    }

    public function test_conversion_uses_constants()
    {
        foreach (DocumentType::$mimeTypesToDocType as $key => $value) {
            $this->assertTrue(DocumentType::isValidEnumValue($value), "$value is not a valid enumeration value");
        }
    }
}
