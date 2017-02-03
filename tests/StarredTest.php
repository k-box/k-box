<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Capability;
use KlinkDMS\Starred;
use Laracasts\TestDummy\Factory;
use Illuminate\Support\Collection;

class StarredTest extends TestCase {

	use DatabaseTransactions;

	public function user_provider(){
		
		return array( 
			array(Capability::$ADMIN, 200),
			array(Capability::$DMS_MASTER, 403),
			array(Capability::$PROJECT_MANAGER, 200),
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
	public function testStarredPageView($caps, $expected_code)
	{

		$this->withKlinkAdapterFake();
		
		$user = $this->createUser($caps);
		
        $starred = factory('KlinkDMS\Starred', 3)->create(['user_id' => $user->id]);
		
		$this->actingAs($user);
		
		$this->visit( route('documents.starred.index') );
             
		if($expected_code === 200){
			$this->assertResponseOk();
            $this->see('Starred');
            $this->seePageIs( route('documents.starred.index') );
		}
		else {
			$view = $this->response->original;
			
			$this->assertEquals('errors.' . $expected_code, $view->name());
		}
		
	}
	
	
	public function testStarIndex()
	{

		$fake = $this->withKlinkAdapterFake();

		$starred_count = 3;
        
		$user = $this->createAdminUser();
		
		$starred = factory('KlinkDMS\Starred', $starred_count)->create(['user_id' => $user->id]);
		
		$this->actingAs($user);
        
        $this->visit( route('documents.starred.index') )->see('Starred');
        
        $s = $this->getInputOrTextAreaValue('s');
        
        $this->assertEmpty($s);
		
        $this->assertResponseOk();

		$this->assertViewHas('starred'); //has the key
		
		$starred_response = $this->response->original->starred;
		
		$this->assertEquals($starred_count, $starred_response->count());
        
		$this->assertEquals($starred->first()->document_id, $starred_response->first()->document_id);
		
	}
	
	public function testAddStar(){

		$this->withKlinkAdapterFake();
		
		$user = $this->createUser(Capability::$CONTENT_MANAGER);
		
        $expected_count = Starred::count() + 1;
        
		$doc = factory('KlinkDMS\DocumentDescriptor')->create(['owner_id' => $user->id]);
		
		\Session::start(); // Start a session for the current test
		
		$this->actingAs($user);
        
        $this->post( route('documents.starred.store'), [
			'institution' => $doc->institution->klink_id,
			'descriptor' => $doc->local_document_id,
			'visibility' => $doc->visibility,
			'_token' => csrf_token()
		])->seeJson([
            'status' => 'created',
        ]);
        
		$this->assertResponseStatus(201);
		
		$this->assertEquals($expected_count, Starred::count());
		
	}
	
	
	public function testRemoveStar(){
		
		$user = $this->createUser(Capability::$CONTENT_MANAGER);
        
        $expected_count = Starred::count();
		
		$starred = factory('KlinkDMS\Starred')->create(['user_id' => $user->id]);
		
		$this->actingAs($user);
		
        \Session::start(); // Start a session for the current test

		$this->delete( route('documents.starred.destroy', [
                'id' => $starred->id, 
                '_token' => csrf_token()])
             )
             ->seeJson([
                 'status' => 'ok'
             ]);
		
		$this->assertResponseOk();
		
		$this->assertEquals($expected_count, Starred::count());
	}

}