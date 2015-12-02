<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Capability;
use Illuminate\Support\Facades\Artisan;
use Laracasts\TestDummy\DbTestCase;

/*
 * Basic tests of the routes that requires the user to be authenticated and with specific capabilities
*/
class RoutesTest extends DbTestCase {

	public function setUp()
	{

		parent::setUp();

	}

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testFreeRoutes()
	{
		
		// $user = Factory::create('KlinkDMS\User');
		// 
		// $admin_user = Factory::create('admin_user');
		// 
		// dd(compact('user', 'admin_user'));
		
		$response = $this->call('GET', '/');

		$this->assertResponseOk();
		
		$response = $this->call('GET', '/search');

		$this->assertResponseOk();
		
		$response = $this->call('GET', '/contact');

		$this->assertResponseOk();
		
		$response = $this->call('GET', '/privacy');

		$this->assertResponseOk();
		
		$response = $this->call('GET', '/terms');

		$this->assertResponseOk();
		
		$response = $this->call('GET', '/help');

		$this->assertResponseOk();
		
// 		$response = $this->call('GET', '/klink/aaaa/document');
// 
// 		$this->assertResponseOk();
// 		
// 		$response = $this->call('GET', '/klink/aaaa/thumbnail');
// 
// 		$this->assertResponseOk();
		
	}
	
}