<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Console\Commands\DmsLanguagePublishCommand;

class LanguagePublishCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testJsLangCreation()
    {
        $files = app('files');
        
        $app = app();
        
        $app['config']['localization.supported_locales'] = ['ru'];
        $app['config']['localization.exports'] = ['validation.accepted'];
        $app['config']['app.fallback_locale'] = 'en';
        
        $command = new DmsLanguagePublishCommand( $files );
        
        $res = $this->runArtisanCommand($command, []);
        
        $this->assertRegExp('/Assembling language files for javascript/', $res);
        $this->assertRegExp('/language files created in public\/js\/nls/', $res);
        
        $expected_files = ['js/nls/lang.js', 'js/nls/ru/lang.js'];
        
        foreach ($expected_files as $p) {
            $this->assertTrue( $files->isFile(public_path($p)), 'JS File not found: ' . $p );
        }
        
        $root_lang = $files->get(public_path('js/nls/lang.js'));
        
        $this->assertRegExp('/ru/', $root_lang);
        $this->assertRegExp('/validation.accepted/', $root_lang);
        $this->assertRegExp('/define/', $root_lang);
        
        $ru_lang = $files->get(public_path('js/nls/ru/lang.js'));
        
        $this->assertRegExp('/validation.accepted/', $ru_lang);
        $this->assertRegExp('/define/', $ru_lang);
        
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Supported languages (localization.supported_locales) should be an array.
     */
    public function testInvalidSupportLocales(){
        
        $app = app();
        
        $app['config']['localization.supported_locales'] = null;
        
        $command = new DmsLanguagePublishCommand( app('files') );
        
        $res = $this->runArtisanCommand($command, []);
        
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expecting non-empty array for localization.exports
     */
    public function testInvalidExports(){
        
        $app = app();
        
        $app['config']['localization.exports'] = null;
        
        $command = new DmsLanguagePublishCommand( app('files') );
        
        $res = $this->runArtisanCommand($command, []);
        
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Empty fallback language set in app.fallback_locale
     */
    public function testInvalidFallbackLocale(){
        
        $app = app();
        
        $app['config']['app.fallback_locale'] = '';
        
        $command = new DmsLanguagePublishCommand( app('files') );
        
        $res = $this->runArtisanCommand($command, []);
        
    }
    
    
    protected function runCommand($command, $input = [], $output = null)
    {
        if(is_null($output)){
             $output = new Symfony\Component\Console\Output\NullOutput;
        }
        
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), $output);
    }
}
