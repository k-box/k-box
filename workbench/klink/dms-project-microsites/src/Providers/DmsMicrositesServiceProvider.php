<?php namespace Klink\DmsMicrosites\Providers;

use Illuminate\Support\ServiceProvider;
use Klink\DmsMicrosites\MicrositeContentParserService;

class DmsMicrositesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the sites views.
	 *
	 * @return void
	 */
	public function boot()
	{
        
        
        
        // tells Laravel where to find the views
        // use view('courier::admin'); to reference them
        $this->loadViewsFrom( realpath(__DIR__.'/../../templates/'), 'sites');
        
        
        /*
            If your package contains translation files, you may use the loadTranslationsFrom 
            method to inform Laravel how to load them. For example, if your package is named 
            "courier", you should add the following to your service provider's boot method:
            
            usage: trans('courier::messages.welcome');
        */    
        // $this->loadTranslationsFrom(__DIR__.'/../../lang', 'sites');
        
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('micrositeparser', function ($app) {
			return new MicrositeContentParserService( $app->make('cache') );
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
            'Klink\DmsMicrosites\Providers\DmsMicrositesServiceProvider',
            'Klink\DmsMicrosites\Controllers\MicrositeController',
            'Klink\DmsMicrosites\Requests\MicrositeCreationRequest',
            'Klink\DmsMicrosites\Microsite',
            'Klink\DmsMicrosites\MicrositeContent',
            'Klink\DmsMicrosites\MicrositeContentParserService',
        ];
	}

}
