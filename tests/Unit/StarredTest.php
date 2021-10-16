<?php

namespace Tests\Unit;

use Carbon\Carbon;
use KBox\User;
use KBox\Starred;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;

class StarredTest extends TestCase
{
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
        
        $user = tap(User::factory()->create(), function ($u) use ($caps) {
            $u->addCapabilities($caps);
        });
        
        $starred = factory(Starred::class, 3)->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->get(route('documents.starred.index'));
             
        if ($expected_code === 200) {
            $response->assertSuccessful();
            $response->assertSee('Starred');
        } else {
            $response->assertStatus($expected_code);
        }
    }

    protected function createRecentDocument(User $user, Carbon $date = null, $documentParams = [])
    {
        return DocumentDescriptor::factory()->create(array_merge([
            'owner_id' => $user->id,
            'created_at' => $date ?? Carbon::now(),
            'updated_at' => $date ?? Carbon::now(),
        ], $documentParams));
    }
    
    public function test_starred_page_shows_starred_documents()
    {
        $this->withKlinkAdapterFake();
        
        $user = User::factory()->admin()->create();

        $created_at = Carbon::now();
        
        $starred = [
            Starred::factory()->create(['user_id' => $user->id, 'document_id' => $this->createRecentDocument($user, $created_at)]),
            Starred::factory()->create(['user_id' => $user->id, 'document_id' => $this->createRecentDocument($user, $created_at->copy()->subMinutes(10))]),
            Starred::factory()->create(['user_id' => $user->id, 'document_id' => $this->createRecentDocument($user, $created_at->copy()->subHours(2))]),
        ];
        
        $response = $this->actingAs($user)->get(route('documents.starred.index'));
        
        $response->assertSuccessful();
        
        $response->assertSee('Starred');
        $response->assertViewHas('search_terms', '*');
        $response->assertDontSee('value="*"');
    
        $response->assertViewHas('starred');
        
        $starred_response = $response->data('starred');
        
        $this->assertEquals(3, $starred_response->count());
        
        $this->assertEquals($starred[0]->document_id, $starred_response->first()->document_id);
    }
    
    public function test_add_star()
    {
        $this->withKlinkAdapterFake();
        
        $user = User::factory()->partner()->create();
        
        $expected_count = Starred::count() + 1;
        
        $doc = DocumentDescriptor::factory()->create([
            'owner_id' => $user->getKey(),
            'local_document_id' => 'stardoc',
        ]);
        
        $response = $this->actingAs($user)->post(route('documents.starred.store'), [
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
        $user = User::factory()->partner()->create();
        
        $expected_count = Starred::count();
        
        $starred = Starred::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->delete(
            route('documents.starred.destroy', [
                'starred' => $starred->id,
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
        
        $user = User::factory()->partner()->create();
        
        $starred = factory(Starred::class, 3)->create(['user_id' => $user->id]);

        $starred->first()->document->delete();

        $response = $this->actingAs($user)->get(route('documents.starred.index'));
        
        $response->assertSuccessful();
    }

    public function test_remove_star_from_trashed_documents()
    {
        $this->withKlinkAdapterFake();
        
        $user = User::factory()->partner()->create();
        
        $expected_count = Starred::count();
        
        $starred = Starred::factory()->create(['user_id' => $user->id]);

        $starred->document->delete();

        $response = $this->actingAs($user)->get(route('documents.starred.index'));
        
        $response->assertSuccessful();

        $response = $this->actingAs($user)->delete(
            route('documents.starred.destroy', [
                'starred' => $starred->id,
                '_token' => csrf_token()])
        )
             ->assertJson([
                 'status' => 'ok'
             ]);
        
        $response->assertSuccessful();
        
        $this->assertEquals($expected_count, Starred::count());
    }
}
