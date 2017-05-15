<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Capability;
use KlinkDMS\User;
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
	
	// public function testSharingEmailNotificationForPeopleGroup()
	// {
	// 	$this->withKlinkAdapterFake();

	// 	Mail::shouldReceive('send')->once();

	// 	$user = $this->createUser( Capability::$PARTNER );
		
	// 	$group = PeopleGroup::create(['user_id' => $user->id, 'name' => 'a group', 'is_institution_group' => false]);

	// 	$target = $this->createUser( Capability::$PARTNER );

	// 	$group->people()->attach($target->id);

	// 	$doc_to_be_shared = $this->createDocument($user);

    //     $this->actingAs($user);

	// 	$data = [
	// 		'with_people' => [$group->id],
	// 		'documents' => [$doc_to_be_shared->id],
	// 	];

	// 	$this->json('POST', route('shares.store'), $data);
	// }

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

	public function testUnshare(){

		$this->withKlinkAdapterFake();

		$user = $this->createUser( Capability::$PARTNER );
		
		$user_target = $this->createUser( Capability::$PARTNER );

        $this->actingAs($user);
		
		$share = factory('KlinkDMS\Shared')->create([
			'user_id' => $user->id,
			'sharedwith_id' => $user_target->id,
		]);

		$this->json('DELETE', route('shares.destroy', ['id' => $share->id]), []);
		$this->seeJson(['status' => 'ok', 'message' => trans('share.removed')]);

	}

	public function testShareCreateDialogWithSingleDocument(){

		$this->withKlinkAdapterFake();

		$user = $this->createUser( Capability::$PARTNER );

		$document = $this->createDocument($user);
		
		$user_target = $this->createUser( Capability::$PARTNER );

        $this->actingAs($user);

		// Load the dialog
		// Assert user target is in the list of available users

		$this->visit(route('shares.create', [
			'collections' => [],
			'documents' => [$document->id]
		]));

		$this->assertViewHas('is_network_enabled', false);
		$this->assertViewHas('can_make_public', false);
		$this->assertViewHas('has_documents', true);
		$this->assertViewHas('has_groups', false);
		$this->assertViewHas('elements_count', 1);
		$this->assertViewHas('is_multiple_selection', false);
		$this->assertViewHas('is_public', false);
		$this->assertViewHas('is_collection', false);
		$this->assertEmpty($this->response->original->existing_shares);
		$this->assertEmpty($this->response->original->groups);
		$this->assertEquals($document->id, $this->response->original->documents->first()->id);
		$this->assertContains($user_target->id, $this->response->original->users->pluck('id'));
		$this->assertNotContains($user->id, $this->response->original->users->pluck('id'));
		
	}

	public function testShareCreateDialogWithMultipleDocuments(){

		$this->withKlinkAdapterFake();

		$user = $this->createUser( Capability::$PARTNER );

		$document1 = $this->createDocument($user);
		$document2 = $this->createDocument($user);
		
		$user_target = $this->createUser( Capability::$PARTNER );

        $this->actingAs($user);

		// Load the dialog
		// Assert user target is in the list of available users

		$this->visit(route('shares.create', [
			'collections' => [],
			'documents' => [$document1->id, $document2->id]
		]));

		$this->assertViewHas('is_network_enabled', false);
		$this->assertViewHas('can_make_public', false);
		$this->assertViewHas('has_documents', true);
		$this->assertViewHas('has_groups', false);
		$this->assertViewHas('elements_count', 2);
		$this->assertViewHas('is_multiple_selection', true);
		$this->assertViewHas('is_public', false);
		$this->assertViewHas('is_collection', false);
		$this->assertEmpty($this->response->original->existing_shares);
		$this->assertEmpty($this->response->original->groups);
		$this->assertEquals([$document1->id, $document2->id], $this->response->original->documents->pluck('id')->toArray());
		$this->assertContains($user_target->id, $this->response->original->users->pluck('id'));
		$this->assertNotContains($user->id, $this->response->original->users->pluck('id'));



	}

	public function testShareCreateDialogWithSingleCollection(){

		$this->withKlinkAdapterFake();

		$user = $this->createUser( Capability::$PARTNER );
		
		$user_target = $this->createUser( Capability::$PARTNER );

		$collection = $this->createCollection($user);

        $this->actingAs($user);

		// Load the dialog
		// Assert user target is in the list of available users

		$this->visit(route('shares.create', [
			'collections' => [$collection->id],
			'documents' => [],
		]));

		$this->assertViewHas('is_network_enabled', false);
		$this->assertViewHas('can_make_public', false);
		$this->assertViewHas('has_documents', false);
		$this->assertViewHas('has_groups', true);
		$this->assertViewHas('elements_count', 1);
		$this->assertViewHas('is_multiple_selection', false);
		$this->assertViewHas('is_public', false);
		$this->assertViewHas('is_collection', true);
		$this->assertEmpty($this->response->original->existing_shares);
		$this->assertEmpty($this->response->original->documents);
		$this->assertEquals([$collection->id], $this->response->original->groups->pluck('id')->toArray());
		$this->assertContains($user_target->id, $this->response->original->users->pluck('id'));
		$this->assertNotContains($user->id, $this->response->original->users->pluck('id'));


	}

	public function testShareCreateDialogWithMixedDocumentsCollections(){

		$this->withKlinkAdapterFake();

		$user = $this->createUser( Capability::$PARTNER );
		
		$user_target = $this->createUser( Capability::$PARTNER );

		$document1 = $this->createDocument($user);
		$document2 = $this->createDocument($user);

		$collection1 = $this->createCollection($user);
		$collection2 = $this->createCollection($user);

        $this->actingAs($user);

		// Load the dialog
		// Assert user target is in the list of available users

		$this->visit(route('shares.create', [
			'collections' => [$collection1->id, $collection2->id],
			'documents' => [$document1->id, $document2->id]
		]));

		$this->assertViewHas('is_network_enabled', false);
		$this->assertViewHas('can_make_public', false);
		$this->assertViewHas('has_documents', true);
		$this->assertViewHas('has_groups', true);
		$this->assertViewHas('elements_count', 4);
		$this->assertViewHas('is_multiple_selection', true);
		$this->assertViewHas('is_public', false);
		$this->assertViewHas('is_collection', false);
		$this->assertEmpty($this->response->original->existing_shares);
		$this->assertEquals([$document1->id, $document2->id], $this->response->original->documents->pluck('id')->toArray());
		$this->assertEquals([$collection1->id, $collection2->id], $this->response->original->groups->pluck('id')->toArray());
		$this->assertContains($user_target->id, $this->response->original->users->pluck('id'));
		$this->assertNotContains($user->id, $this->response->original->users->pluck('id'));

	}

}