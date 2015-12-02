<?php namespace Klink\DmsPreviews;

use Illuminate\Support\ServiceProvider;

class DmsPreviewsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// $this->package('klink/dms-documents');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('previewsservice', function ($app) {

			return new Klink\DmsPreviews\PreviewsService;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['Klink\DmsPreviews\PreviewsService'];
	}

}
