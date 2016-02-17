<?php

use KlinkDMS\User;
use KlinkDMS\Capability;
use KlinkDMS\Shared;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests user handling
*/
class UsersTest extends TestCase {

// 	public function setUp()
// 	{
// 
// 		parent::setUp();
// 
// 		$artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
// 
// 		// $artisan->call('migrate',array('-n'=>true));
// 
// 		// $artisan->call('db:seed',array('-n'=>true));
// 		//dd(env('DB_NAME'));
// 	}
// 
// 	/**
// 	 * Tests the basic atomic operations around a user creation, capability checking and softdeleting
// 	 *
// 	 * @return void
// 	 */
// 	public function testUserSeeding()
// 	{
// 		$this->createAdminUser();
// 
// 		// check users count
// 		$this->assertEquals(1, User::count());
// 		
// 		// find by ...
// 		
// 		$find_by_name = User::findByName('Cooter');
// 		
// 		$this->assertNull($find_by_name);
// 		
// 		$find_by_name = User::findByName('admin');
// 		
// 		$this->assertNotNull($find_by_name);
// 		$this->assertEquals('admin', $find_by_name->name);
// 		$this->assertEquals('admin@klink.local', $find_by_name->email);
// 		
// 		
// 		$user = User::findByEmail('admin@klink.local');
// 		
// 		$this->assertNotNull($user);
// 		$this->assertEquals('admin', $user->name);
// 		$this->assertEquals('admin@klink.local', $user->email);
// 		
// 		// basic user config checks
// 		
// 		$this->assertEquals('http://localhost/administration', $user->homeRoute());
// 		
// 		$this->assertTrue($user->isContentManager());
// 		
// 		$this->assertTrue($user->isDMSAdmin());
// 		
// 		$this->assertTrue($user->isDMSManager());
// 		
// 		// test has capability
// 		
// 		$this->assertTrue($user->can_capability(Capability::UPLOAD_DOCUMENTS));
// 		
// 		// test has all capabilities
// 		$this->assertTrue($user->can_capability(Capability::$ADMIN));
// 		
// 		// test remove capability (1 or more)
// 		
// 		$user->removeCapability(Capability::UPLOAD_DOCUMENTS);
// 		
// 		$this->assertFalse($user->can_capability(Capability::UPLOAD_DOCUMENTS), 'Removed UPLOAD_DOCUMENTS capability');
// 		$this->assertFalse($user->can_capability(Capability::$ADMIN), 'Test all ADMIN capavbilities after removing UPLOAD_DOCUMENTS');
// 		
// 		$user->addCapability(Capability::UPLOAD_DOCUMENTS);
// 		
// 		$this->assertTrue($user->can_capability(Capability::UPLOAD_DOCUMENTS), 'Adding back a capability');
// 		
// 		
// 		// test delete (must be a softdeleted record)
// 		
// 		$user->delete();
// 		
// 		//assert if is trashed
// 		$this->assertTrue($user->trashed());
// 		
// 		
// 		// completed, basic tests around User and Capability models + HasCapability trait
// 	}
// 	
// 	/**
// 	 * Tests the relations between the User model and the other entities
// 	 */
// 	public function testUserRelations(){
// 		// create a fake user
// 		// add some fake documents and searches and stuff like that
// 		// tests the relationships methods
// 		
// 		$user = $this->createAdminUser();
// 		
// 		$starred = Factory::create('KlinkDMS\Starred', ['user_id' => $user->id]);
// 		
// 		$groups = Factory::create('KlinkDMS\PeopleGroup', ['user_id' => $user->id]);
// 		
// 		$docs = Factory::create('KlinkDMS\DocumentDescriptor', ['owner_id' => $user->id]);
// 		
// 		$searches = Factory::create('KlinkDMS\RecentSearch', ['user_id' => $user->id]);
// 		
// 		$shared = Factory::create('KlinkDMS\Shared', ['user_id' => $user->id]);
// 		
// 		$shared_groups = Factory::create('KlinkDMS\Shared', ['token' => 'shared_group', 'user_id' => $user->id, 'sharedwith_id' => $groups->id, 'sharedwith_type' =>'KlinkDMS\PeopleGroup']);
// 		
// 		$user = $user->fresh();
// 		
// 		$this->assertEquals(1, $user->starred()->count(), 'Starred count');
// 		$this->assertEquals(1, $user->documents()->count(), 'Documents count');
// 		$this->assertEquals(2, $user->shares()->count(), 'Shares count');
// 		$this->assertEquals(1, Shared::sharedByMe($user)->sharedWithGroup($groups)->count(), 'Shares in groups count');
// 		
// 		
// 	}
// 	
// 	public function option_name_value_provider(){
// 		
// 		return array(
// 			array('test_string', 'value'),
// 			array('test_int', 10),
// 			array('test_float', 10.0),
// 		);
// 	}
// 	
// 	/**
// 		@dataProvider option_name_value_provider
// 	*/
// 	public function testUserOptions($name, $value){
// 		
// 		$user = $this->createAdminUser();
// 		
// 		$test_opt = $user->getOption($name);
// 		
// 		$this->assertNull($test_opt);
// 		
// 		$user->setOption($name, $value);
// 		
// 		$test_opt = $user->getOption($name);
// 		
// 		$this->assertEquals($value, $test_opt->value);
// 		
// 	}
// 	
// 	public function testUserAdministrationController(){
// 		$this->markTestIncomplete(
//           'This test has not been implemented yet.'
//         );
// 	}
	

}