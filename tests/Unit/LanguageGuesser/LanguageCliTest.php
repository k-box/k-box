<?php

namespace Tests\Unit\LanguageGuesser;

use Tests\TestCase;
use OneOffTech\LanguageGuesser\Drivers\LanguageCli;

class LanguageCliTest extends TestCase
{
    protected function setUp()
    {
        if (empty(glob('./bin/language-guesser*'))) {
            $this->markTestSkipped(
              'Language Guesser not installed.'
            );
        }

        parent::setUp();
    }

    public function test_language_cli_can_guess_language()
    {
        $cli = new LanguageCli('This is an example text for verifying that the language guesser can be invoked');

        $output = $cli->run();

        $this->assertNotEmpty('eng', $output);
        $this->assertEquals(3, strlen($output));
    }
    
    public function test_language_cli_can_guess_language_with_blacklist()
    {
        $cli = new LanguageCli('This is an example text for verifying that the language guesser can be invoked', false, ['sco']);

        $output = $cli->run();

        $this->assertEquals('eng', $output);
    }
    
    public function test_language_cli_can_guess_language_with_all()
    {
        $cli = new LanguageCli('This is an example text for verifying that the language guesser can be invoked', true, ['sco']);

        $output = $cli->run();

        $this->assertRegExp('/.{3}\s\d/m', $output);
    }
}
