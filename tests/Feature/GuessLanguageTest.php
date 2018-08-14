<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\Documents\KlinkDocumentUtils;
use KBox\DocumentsElaboration\Actions\GuessLanguage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GuessLanguageTest extends TestCase
{
    use DatabaseTransactions;

    private function generateDescriptorForFile($path)
    {
        $file = factory(\KBox\File::class)->create([
            'hash' => hash_file('sha512', $path),
            'path' => $path,
            'mime_type' => KlinkDocumentUtils::get_mime($path),
        ]);
        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'file_id' => $file->id,
            'language' => null,
            'abstract' => null,
        ]);

        return $descriptor;
    }

    public function test_language_can_be_guessed_from_plain_text_file()
    {
        $this->disableExceptionHandling();

        // create descriptor with text file
        $descriptor = $this->generateDescriptorForFile(base_path('tests/data/example.txt'));

        // instantiate the GuessLanguage action
        $action = new GuessLanguage();

        $out_descriptor = $action->run($descriptor);

        // obtaining a fresh copy to confirm that the action saved the language in the database
        $descriptor = $descriptor->fresh();
        
        $this->assertNotEmpty($descriptor->language);
        $this->assertEquals(2, strlen($descriptor->language));
        $this->assertEquals('en', $descriptor->language);
    }
    
    public function test_language_can_be_guessed_from_pdf()
    {
        $this->disableExceptionHandling();

        // create descriptor with text file
        $descriptor = $this->generateDescriptorForFile(base_path('tests/data/example.pdf'));

        // instantiate the GuessLanguage action
        $action = new GuessLanguage();

        $out_descriptor = $action->run($descriptor);

        // obtaining a fresh copy to confirm that the action saved the language in the database
        $descriptor = $descriptor->fresh();
        
        $this->assertNotEmpty($descriptor->language);
        $this->assertEquals(2, strlen($descriptor->language));
        // not asserting that language is english as the text content is too short for a reliable guessing
    }
    
    public function test_language_can_be_guessed_from_word()
    {
        $this->disableExceptionHandling();

        // create descriptor with text file
        $descriptor = $this->generateDescriptorForFile(base_path('tests/data/example-with-multiline.docx'));

        // instantiate the GuessLanguage action
        $action = new GuessLanguage();

        $out_descriptor = $action->run($descriptor);

        // obtaining a fresh copy to confirm that the action saved the language in the database
        $descriptor = $descriptor->fresh();
        
        $this->assertNotEmpty($descriptor->language);
        $this->assertEquals(2, strlen($descriptor->language));
        $this->assertEquals('en', $descriptor->language);
    }
    
    public function test_language_can_be_guessed_from_presentation()
    {
        $this->disableExceptionHandling();

        // create descriptor with text file
        $descriptor = $this->generateDescriptorForFile(base_path('tests/data/example-presentation-simple.pptx'));

        // instantiate the GuessLanguage action
        $action = new GuessLanguage();

        $out_descriptor = $action->run($descriptor);

        // obtaining a fresh copy to confirm that the action saved the language in the database
        $descriptor = $descriptor->fresh();
        
        $this->assertNotEmpty($descriptor->language);
        $this->assertEquals(2, strlen($descriptor->language));
    }
    
    public function test_language_not_set_if_file_unsupported()
    {
        $this->disableExceptionHandling();

        // create descriptor with text file
        $descriptor = $this->generateDescriptorForFile(base_path('tests/data/video.mp4'));

        // instantiate the GuessLanguage action
        $action = new GuessLanguage();

        $out_descriptor = $action->run($descriptor);

        // obtaining a fresh copy to confirm that the action saved the language in the database
        $descriptor = $descriptor->fresh();
        
        $this->assertNull($descriptor->language);
    }

    public function test_language_not_guessed_if_already_specified()
    {
        $this->disableExceptionHandling();

        // create descriptor with text file
        $descriptor = $this->generateDescriptorForFile(base_path('tests/data/example.txt'));
        $descriptor->language = 'it';
        $descriptor->save();

        // instantiate the GuessLanguage action
        $action = new GuessLanguage();

        $out_descriptor = $action->run($descriptor);

        // obtaining a fresh copy to confirm that the action saved the language in the database
        $descriptor = $descriptor->fresh();
        
        $this->assertNotEmpty($descriptor->language);
        $this->assertEquals(2, strlen($descriptor->language));
        $this->assertEquals('it', $descriptor->language);
    }

    public function test_language_guesser_respect_language_whitelist()
    {
        config(['dms.language_whitelist' => ['de']]);
        // create descriptor with text file
        $descriptor = $this->generateDescriptorForFile(base_path('tests/data/example-with-multiline.docx'));

        // instantiate the GuessLanguage action
        $action = new GuessLanguage();

        $out_descriptor = $action->run($descriptor);

        // obtaining a fresh copy to confirm that the action saved the language in the database
        $descriptor = $descriptor->fresh();

        $this->assertNull($descriptor->language);
    }
}
