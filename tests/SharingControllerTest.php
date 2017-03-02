<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Capability;
use KlinkDMS\Shared;
use KlinkDMS\PeopleGroup;
use Laracasts\TestDummy\Factory;
use Illuminate\Support\Collection;

class SharingControllerTest extends TestCase {

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

	public function share_link_redirect(){
		
		return array( 
			array(Capability::$ADMIN, 'collection', 'documents.groups.show'),
			array(Capability::$ADMIN, 'descriptor', 'documents.sharedwithme'),
			array(Capability::$PROJECT_MANAGER, 'collection', 'documents.groups.show'),
			array(Capability::$PROJECT_MANAGER, 'descriptor', 'documents.sharedwithme'),
			array(Capability::$PARTNER, 'collection', 'documents.groups.show'),
			array(Capability::$PARTNER, 'descriptor', 'documents.sharedwithme'),
			array(Capability::$GUEST, 'collection', 'shares.group'),
			array(Capability::$GUEST, 'descriptor', 'shares.index'),
		);
	} 


	public function testShareCreatedEvent()
	{

		$this->withKlinkAdapterFake();

		$this->expectsEvents(KlinkDMS\Events\ShareCreated::class);

		$user = $this->createUser( Capability::$PARTNER );
		
		$user_target = $this->createUser( Capability::$PARTNER );

		$doc_to_be_shared = $this->createDocument($user);

		// using a partner user, create a document and share it with a second user
		// at the end the ShareCreated event must be raised

        $this->actingAs($user);

		$data = [
			'with_users' => [$user_target->id],
			'documents' => [$doc_to_be_shared->id],
		];

		$this->json('POST', route('shares.store'), $data)
		     ->seeJson(['status' => 'ok']);

	}


	public function testSharingEmailNotificationSendCalled()
	{
		$this->withKlinkAdapterFake();

		Mail::shouldReceive('send')->once();

		$user = $this->createUser( Capability::$PARTNER );
		
		$user_target = $this->createUser( Capability::$PARTNER );

		$doc_to_be_shared = $this->createDocument($user);

        $this->actingAs($user);

		$data = [
			'with_users' => [$user_target->id],
			'documents' => [$doc_to_be_shared->id],
		];

		$this->json('POST', route('shares.store'), $data);
	}
	
	public function testSharingEmailNotificationForPeopleGroup()
	{
		$this->withKlinkAdapterFake();

		Mail::shouldReceive('send')->once();

		$user = $this->createUser( Capability::$PARTNER );
		
		$group = PeopleGroup::create(['user_id' => $user->id, 'name' => 'a group', 'is_institution_group' => false]);

		$target = $this->createUser( Capability::$PARTNER );

		$group->people()->attach($target->id);

		$doc_to_be_shared = $this->createDocument($user);

        $this->actingAs($user);

		$data = [
			'with_people' => [$group->id],
			'documents' => [$doc_to_be_shared->id],
		];

		$this->json('POST', route('shares.store'), $data);
	}

	/**
	 * @dataProvider share_link_redirect
	 */
	public function testSharingLinkRedirect($target_capabilities, $share_what, $expected_route_name)
	{
		$this->withKlinkAdapterFake();

		$this->expectsEvents(KlinkDMS\Events\ShareCreated::class);

		$user = $this->createUser( Capability::$PARTNER );
		
		$user_target = $this->createUser( $target_capabilities );


		$to_be_shared = null;

		if($share_what==='collection'){
			$to_be_shared = $this->createCollection($user);
		}
		else {
			$to_be_shared = $this->createDocument($user);
		}

        $this->actingAs($user);

		$data = [
			'with_users' => [$user_target->id],
		];

		if($share_what==='collection'){
			$data['groups'] = [$to_be_shared->id];
		} 
		else 
		{
			$data['documents'] = [$to_be_shared->id];
		}

		$already_created = Shared::all()->pluck('id');

		$this->json('POST', route('shares.store'), $data);

		$after = Shared::all()->pluck('id')->diff($already_created);

		$share = Shared::findOrFail($after->first());

		$url = route('shares.show', ['id' => $share->token]);


		$params = [];

		if($share_what==='collection'){
			$params['id'] = $to_be_shared->id;
		} 
		else 
		{
			$params['highlight'] = $to_be_shared->id;
		}

		$this->actingAs($user_target);
		
		$this->visit($url)->seePageIs(route($expected_route_name, $params));

	}

	/**
	 * Test what happens if the same document is shared twice
	 */
	public function testSharingTwice(){

		$this->withKlinkAdapterFake();

		Mail::shouldReceive('send')->once();

		$user = $this->createUser( Capability::$PARTNER );
		
		$user_target = $this->createUser( Capability::$PARTNER );

		$doc_to_be_shared = $this->createDocument($user);

        $this->actingAs($user);

		$data = [
			'with_users' => [$user_target->id],
			'documents' => [$doc_to_be_shared->id],
		];

		$this->json('POST', route('shares.store'), $data);
		
		$this->json('POST', route('shares.store'), $data);

	}

}