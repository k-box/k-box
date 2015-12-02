<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Capability;
use Illuminate\Support\Facades\Artisan;

class TestCase extends Illuminate\Foundation\Testing\TestCase {


	protected $artisan = null;
	
	public function setUp()
	{

		parent::setUp();

		$artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');

		// $artisan->call('migrate',array('-n'=>true));

		// $artisan->call('db:seed',array('-n'=>true));
		//dd(env('DB_NAME'));
		
		//let's create some users to simulate different kinds of users
	}

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$app = require __DIR__.'/../bootstrap/app.php';

		$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

		return $app;
	}
	
	
	protected function createAdminUser(){
		
		$admin_user = Factory::create('admin_user');
		
		$admin_user->addCapabilities( Capability::$ADMIN );
		
		return $admin_user;
	}
	
	protected function createUser($capabilities){
		
		$user = Factory::create('KlinkDMS\User');
		
		$user->addCapabilities( $capabilities );
		
		return $user;
	}

}
