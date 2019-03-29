<?php

namespace Tests\Unit;

use KBox\User;
use KBox\Starred;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StarredTest extends TestCase
{
    use DatabaseTransactions;

    public function user_provider()
    {
        return [
            [Capability::$ADMIN, 200],
            [[Capability::MANAGE_KBOX], 403],
            [Capability::$PROJECT_MANAGER, 200],
            [Capability::$PARTNER, 200],
            [[Capability::RECEIVE_AND_SEE_SHARE], 403],
        ];
    }

    /**
     * @dataProvider user_provider
     */
    public function test_starred_page_view($caps, $expected_code)
    {
        $this->withKlinkAdapterFake();
        
        $user = tap(factory(User::class)->create(), function ($u) use ($caps) {
            $u->addCapabilities($caps);
        });
        
        $starred = factory(Starred::class, 3)->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->get(route('documents.starred.index'));
             
        if ($expected_code === 200) {
            $response->assertSuccessful();
            $response->assertSee('Starred');
        } else {
            $response->assertViewIs('errors.'.$expected_code);
        }
    }
    
    public function test_starred_page_shows_starred_documents()
    {
        $fake = $this->withKlinkAdapterFake();

        $starred_count = 3;
        
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $starred = factory(Starred::class, $starred_count)->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->get(route('documents.starred.index'));
        
        $response->assertSuccessful();
        
        $response->assertSee('Starred');
        $response->assertViewHas('search_terms', '*');
        $response->assertDontSee('value="*"');
    
        $response->assertViewHas('starred'); //has the key
        
        $starred_response = $response->data('starred');
        
        $this->assertEquals($starred_count, $starred_response->count());
        
        $this->assertEquals($starred->first()->document_id, $starred_response->first()->document_id);
    }
    
    public function test_add_star()
    {
        $this->withKlinkAdapterFake();
        
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $expected_count = Starred::count() + 1;
        
        $doc = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);
        
        $response = $this->actingAs($user)->post(route('documents.starred.store'), [
            'institution' => config('dms.institutionID'),
            'descriptor' => $doc->local_document_id,
            'visibility' => $doc->visibility,
            '_token' => csrf_token()
        ])->assertJson([
            'status' => 'created',
        ]);
        
        $response->assertStatus(201);
        
        $this->assertEquals($expected_count, Starred::count());
    }
    
    public function test_remove_star()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $expected_count = Starred::count();
        
        $starred = factory(Starred::class)->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->delete(
            route('documents.starred.destroy', [
                'id' => $starred->id,
                '_token' => csrf_token()])
        )
             ->assertJson([
                 'status' => 'ok'
             ]);
        
        $response->assertSuccessful();
        
        $this->assertEquals($expected_count, Starred::count());
    }

    public function test_starred_page_loads_with_trashed_documents()
    {
        $this->withKlinkAdapterFake();
        
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $starred = factory(Starred::class, 3)->create(['user_id' => $user->id]);

        $starred->first()->document->delete();

        $response = $this->actingAs($user)->get(route('documents.starred.index'));
        
        $response->assertSuccessful();
    }

    public function test_remove_star_from_trashed_documents()
    {
        $this->withKlinkAdapterFake();
        
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });
        
        $expected_count = Starred::count();
        
        $starred = factory(Starred::class)->create(['user_id' => $user->id]);

        $starred->document->delete();

        $response = $this->actingAs($user)->get(route('documents.starred.index'));
        
        $response->assertSuccessful();

        $response = $this->actingAs($user)->delete(
            route('documents.starred.destroy', [
                'id' => $starred->id,
                '_token' => csrf_token()])
        )
             ->assertJson([
                 'status' => 'ok'
             ]);
        
        $response->assertSuccessful();
        
        $this->assertEquals($expected_count, Starred::count());
    }
}
