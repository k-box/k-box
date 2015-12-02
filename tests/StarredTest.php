<?php

use Laracasts\TestDummy\DbTestCase;
use KlinkDMS\Capability;
use KlinkDMS\Starred;
use Laracasts\TestDummy\Factory;
use Illuminate\Support\Collection;

class StarredTest extends DbTestCase {

	public function setUp()
	{

		parent::setUp();
		
	}


	public function user_provider(){
		
		return array( 
			array(Capability::$ADMIN, 200),
			array(Capability::$DMS_MASTER, 200),
			array(Capability::$CONTENT_MANAGER, 200),
			array(Capability::$PARTNER, 200),
			array(Capability::$GUEST, 403),
		);
	} 

	/**
	 * A basic functional test example.
	 *
	 * @dataProvider user_provider
	 * @return void
	 */
	public function testPageView($caps, $expected_code)
	{
		
		$user = $this->createUser($caps);
		
		$doc = Factory::create('KlinkDMS\Starred', ['user_id' => $user->id]);
		
		$this->be($user);
		
		$response = $this->call('GET', '/documents/starred');

		if($expected_code === 200){
			$this->assertResponseOk();
		}
		else {
			$view = $this->response->original;
			
			$this->assertEquals('errors.' . $expected_code, $view->name());
		}
		
	}
	
	
	public function testStarIndex()
	{
		
		$user = $this->createUser(Capability::$CONTENT_MANAGER);
		
		$doc = Factory::create('KlinkDMS\Starred', ['user_id' => $user->id]);
		
		$this->be($user);
		
		$response = $this->route('GET', 'documents.starred.index');

		$this->assertViewHas('starred'); //has the key
		
		$starred_response = $this->response->original->starred;
		
		$this->assertEquals(1, $starred_response->count());
		
		$this->assertEquals($doc->id, $starred_response->first()->id);
		
	}
	
	public function testAddStar(){
		
		$user = $this->createUser(Capability::$CONTENT_MANAGER);
		
		$doc = Factory::create('KlinkDMS\DocumentDescriptor',['owner_id' => $user->id]);
		
		\Session::start(); // Start a session for the current test
		
		$this->be($user);
		
		$response = $this->route('POST', 'documents.starred.store', [], [
			'institution' => $doc->institution->klink_id,
			'descriptor' => $doc->local_document_id,
			'visibility' => $doc->visibility,
			'_token' => csrf_token()
		]);
		
		$this->assertResponseStatus(201);
		
		$this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
		
		$this->assertEquals(1, Starred::count());
		
	}
	
	
	public function testRemoveStar(){
		
		// $this->markTestIncomplete(
        //   'This test has not been implemented yet.'
        // );
		
		$user = $this->createUser(Capability::$CONTENT_MANAGER);
		
		$doc = Factory::create('KlinkDMS\Starred', ['user_id' => $user->id]);
		
		$this->be($user);
		
		$this->assertEquals(1, Starred::count());
		
        \Session::start(); // Start a session for the current test

		$response = $this->route('DELETE', 'documents.starred.destroy', ['id' => $doc->id, '_token' => csrf_token()]);
		
		$this->assertResponseOk();
		
		$this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
		
		$this->assertEquals(array('status' => 'ok'), $response->getData(true));
		
		$this->assertEquals(0, Starred::count());
	}

}