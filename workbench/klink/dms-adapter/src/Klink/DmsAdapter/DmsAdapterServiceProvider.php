<?php namespace Klink\DmsAdapter;

use Illuminate\Support\ServiceProvider;

use Klink\DmsAdapter\Contracts\KlinkAdapter as KlinkAdapterContract;
use Klink\DmsAdapter\KlinkAdapter;

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
	 * Register the services offered by the provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app->singleton('klinkadapter', function ($app) {
			return new KlinkAdapter;
		});

		$this->app->bind(KlinkAdapterContract::class, 'klinkadapter');

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			KlinkAdapterContract::class, 
            KlinkAdapter::class, 
            'klinkadapter'
		];
	}

}
