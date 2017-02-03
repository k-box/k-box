<?php namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Foundation\Bus\DispatchesCommands;
use KlinkDMS\Console\Traits\DebugOutput;

use Illuminate\Filesystem\Filesystem;

class DmsLanguagePublishCommand extends Command {

	use DispatchesCommands, DebugOutput;

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dms:lang-publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish Javascript language files for RequireJS i18n plugin.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		parent::__construct();

		$this->files = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        
        $this->comment('Assembling language files for javascript...');
        
        $fallback_locale = config('app.fallback_locale');
        
        $supported = config('localization.supported_locales');
        
        $exports = config('localization.exports');
        
        
        if(!is_array($supported)){
            throw new \InvalidArgumentException('Supported languages (localization.supported_locales) should be an array.');
        }
        
        if(!is_array($exports) || empty($exports)){
            throw new \InvalidArgumentException('Expecting non-empty array for localization.exports');
        }
        
        if(empty($fallback_locale)){
            throw new \InvalidArgumentException('Empty fallback language set in app.fallback_locale');
        }
        
        // Creating the default language file
        
        $this->debugLine('Creating root language file...');
        
        $fallback_exports = $this->getLocalizedExports($exports, $fallback_locale);
        
        $js_ready_supported = '';
        
        if(!empty($supported)){
            $js_ready_supported = ',' . implode(',', array_map(function($l){
                return '"'.$l.'":true';
            }, $supported));
            
        }
        
        $this->createRootLang($fallback_exports, $js_ready_supported);
        
        $this->debugLine('   done.');
        
        // Creating the file for each specific locale
        
        
        
        $lang_export = null;
        foreach ($supported as $language) {
            
            $this->debugLine('Creating '. $language .' language file...');
            
            $lang_export = $this->getLocalizedExports($exports, $language);
            
            $this->createLangFile($lang_export, $language);
            
            $this->debugLine('   done.');
            
        }
        
        
        $this->info('   language files created in public/js/nls/');
        
        return 0;
		
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			// ['example', InputArgument::OPTIONAL, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			// ['single', null, InputOption::VALUE_OPTIONAL, 'Generate a model for the given single migration file.', null],
		];
	}

	/**
	 * The path where to save the newly created models
	 * @return string the path where to save the models
	 */
	public function getModelPath()
	{
		return $this->laravel['path'] . '/';
	}

	/**
	 * The path to the JS stub folder
	 * @return string the path to the javascript stubs folder
	 */
	private function getStubsPath()
	{
		return __DIR__.'/stubs';
	}

	/**
	 * Get the Model stub
	 * @param  string $name the stub name
	 * @return string        the content of the stub
	 */
	private function getStub( $name )
	{
        return $this->files->get($this->getStubsPath(). '/' . $name);
	}
    
    
    /**
     * Creates the Root lang.js file in its final location inside the public folder 
     */
    private function createRootLang($locale, $languages){
        $stub = $this->getStub( 'root-lang.stub' );
        
        $stub = str_replace('%locale%', json_encode($locale), $stub);
        $stub = str_replace('%languages%', $languages, $stub);
        
        $path = public_path('js/nls/lang.js');
        $folder = public_path('js/nls/');
        
        if(!$this->files->exists($folder)){
            $this->files->makeDirectory($folder, 493, true);
        }
        
        $this->files->put( $path, $stub);
    }
    
    /**
     * Creates the Locale specific lang.js file in its final location inside the public folder 
     */
    private function createLangFile($locale, $language){
        $stub = $this->getStub( 'locale-lang.stub' );
        
        $stub = str_replace('%locale%', json_encode($locale), $stub);
        
        $path = public_path('js/nls/' . $language . '/lang.js');
        
        $folder = public_path('js/nls/' . $language . '/');
        
        if(!$this->files->exists($folder)){
            
            $this->files->makeDirectory($folder, 493, true);
            
        }
        
        $this->files->put( $path, $stub);
    }
    
    /**
     * Get the localized version of the exports
     */
    protected function getLocalizedExports($exports, $language){
        
        $back = app()->getLocale();
        
        app()->setLocale($language);
        
        $localized = array_map(function($s){
            
            try{
                
                return trans($s);
                
            }catch(\Exception $ex){
                
                return "";
                
            }
            
        }, $exports);
        
        app()->setLocale($back);
        
        return array_combine($exports, $localized);
        
    }

}
