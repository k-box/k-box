<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KBox\Capability;
use KBox\Starred;
use Laracasts\TestDummy\Factory;

class StarredTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function user_provider()
    {
        return [
            [Capability::$ADMIN, 200],
            [Capability::$DMS_MASTER, 403],
            [Capability::$PROJECT_MANAGER, 200],
            [Capability::$PARTNER, 200],
            [Capability::$GUEST, 403],
        ];
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
        
        $starred = factory(\KBox\Starred::class, 3)->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $this->visit(route('documents.starred.index'));
             
        if ($expected_code === 200) {
            $this->assertResponseOk();
            $this->see('Starred');
            $this->seePageIs(route('documents.starred.index'));
        } else {
            $view = $this->response->original;
            
            $this->assertEquals('errors.'.$expected_code, $view->name());
        }
    }
    
    public function testStarIndex()
    {
        $fake = $this->withKlinkAdapterFake();

        $starred_count = 3;
        
        $user = $this->createAdminUser();
        
        $starred = factory(\KBox\Starred::class, $starred_count)->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $this->visit(route('documents.starred.index'))->see('Starred');
        
        $this->assertViewHas('search_terms', '*');
        $this->dontSee('value="*"');
        
        $this->assertResponseOk();

        $this->assertViewHas('starred'); //has the key
        
        $starred_response = $this->response->original->starred;
        
        $this->assertEquals($starred_count, $starred_response->count());
        
        $this->assertEquals($starred->first()->document_id, $starred_response->first()->document_id);
    }
    
    public function testAddStar()
    {
        $this->withKlinkAdapterFake();
        
        $user = $this->createUser(Capability::$CONTENT_MANAGER);
        
        $expected_count = Starred::count() + 1;
        
        $doc = factory(\KBox\DocumentDescriptor::class)->create(['owner_id' => $user->id]);
        
        \Session::start(); // Start a session for the current test
        
        $this->actingAs($user);
        
        $this->post(route('documents.starred.store'), [
            'institution' => config('dms.institutionID'),
            'descriptor' => $doc->local_document_id,
            'visibility' => $doc->visibility,
            '_token' => csrf_token()
        ])->seeJson([
            'status' => 'created',
        ]);
        
        $this->assertResponseStatus(201);
        
        $this->assertEquals($expected_count, Starred::count());
    }
    
    public function testRemoveStar()
    {
        $user = $this->createUser(Capability::$CONTENT_MANAGER);
        
        $expected_count = Starred::count();
        
        $starred = factory(\KBox\Starred::class)->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        \Session::start(); // Start a session for the current test

        $this->delete(
            route('documents.starred.destroy', [
                'id' => $starred->id,
                '_token' => csrf_token()])
        )
             ->seeJson([
                 'status' => 'ok'
             ]);
        
        $this->assertResponseOk();
        
        $this->assertEquals($expected_count, Starred::count());
    }

    public function test_starred_page_loads_with_trashed_documents()
    {
        $this->withKlinkAdapterFake();
        
        $user = $this->createUser(Capability::$PARTNER);
        
        $starred = factory(\KBox\Starred::class, 3)->create(['user_id' => $user->id]);

        $starred->first()->document->delete();

        $this->actingAs($user);
        
        $this->visit(route('documents.starred.index'));
        
        $this->assertResponseOk();
        $this->seePageIs(route('documents.starred.index'));
    }

    public function test_remove_star_from_trashed_documents()
    {
        $this->withKlinkAdapterFake();
        
        $user = $this->createUser(Capability::$PARTNER);
        
        $expected_count = Starred::count();
        
        $starred = factory(\KBox\Starred::class)->create(['user_id' => $user->id]);

        $starred->document->delete();

        $this->actingAs($user);
        \Session::start(); // Start a session for the current test
        
        $this->visit(route('documents.starred.index'));

        $this->delete(
            route('documents.starred.destroy', [
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
