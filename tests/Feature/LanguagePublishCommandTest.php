<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LanguagePublishCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function testJsLangCreation()
    {
        $this->withoutExceptionHandling();

        $files = app('files');
        
        $app = app();
        
        $app['config']['localization.supported_locales'] = ['ru'];
        $app['config']['localization.exports'] = ['validation.accepted'];
        $app['config']['app.fallback_locale'] = 'en';

        $this->artisan('dms:lang-publish')
            ->expectsOutput('Assembling language files for javascript...')
            ->expectsOutput('   language files created in public/js/nls/')
            ->assertExitCode(0);
        
        $expected_files = ['js/nls/lang.js', 'js/nls/ru/lang.js'];
        
        foreach ($expected_files as $p) {
            $this->assertTrue($files->isFile(public_path($p)), 'JS File not found: '.$p);
        }
        
        $root_lang = $files->get(public_path('js/nls/lang.js'));
        
        $this->assertRegExp('/ru/', $root_lang);
        $this->assertRegExp('/validation.accepted/', $root_lang);
        $this->assertRegExp('/define/', $root_lang);
        
        $ru_lang = $files->get(public_path('js/nls/ru/lang.js'));
        
        $this->assertRegExp('/validation.accepted/', $ru_lang);
        $this->assertRegExp('/define/', $ru_lang);
    }
    
    public function testInvalidSupportLocales()
    {
        $app = app();
        
        $app['config']['localization.supported_locales'] = null;
        
        $this->artisan('dms:lang-publish')
            ->expectsOutput('Supported languages (localization.supported_locales) should be an array.')
            ->assertExitCode(127);
    }
    
    public function testInvalidExports()
    {
        $app = app();
        
        $app['config']['localization.exports'] = null;
        
        $this->artisan('dms:lang-publish')
            ->expectsOutput('Expecting non-empty array for localization.exports')
            ->assertExitCode(127);
    }
    
    public function testInvalidFallbackLocale()
    {
        $app = app();
        
        $app['config']['app.fallback_locale'] = '';
        
        $this->artisan('dms:lang-publish')
            ->expectsOutput('Empty fallback language set in app.fallback_locale')
            ->assertExitCode(127);
    }
}
