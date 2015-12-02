<?php namespace Klink\DmsAdapter;

use Illuminate\Support\ServiceProvider;

class DmsAdapterServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{


		// $this->package('klink/dms-adapter');
		// 
		
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// require_once __DIR__.'/../../../vendor/autoload.php';

		$this->app->singleton('klinkadapter', function ($app) {

			return new \Klink\DmsAdapter\KlinkAdapter;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['\Klink\DmsAdapter\KlinkAdapter'];
	}

}
