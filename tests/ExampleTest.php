<?php

use Laracasts\TestDummy\DbTestCase;

class ExampleTest extends DbTestCase {

	public function setUp()
	{

		parent::setUp();
		
	}

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		
		$response = $this->call('GET', '/');

		$this->assertResponseOk();
		
		// $this->assertResponseStatus(403);
		
// 		$this->assertRedirectedTo('foo');
// 
// 		$this->assertRedirectedToRoute('route.name');
// 		
// 		$this->assertRedirectedToAction('Controller@method');

		// $this->assertViewHas('name');
		// $this->assertViewHas('age', $value);
		// 
		// // logging in as user
		// $user = new User(['name' => 'John']);
		// 
		// $this->be($user);
		
	}

}