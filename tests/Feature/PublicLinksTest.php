<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KBox\Capability;
use KBox\User;
use KBox\Shared;
use KBox\DocumentDescriptor;
use KBox\Events\ShareCreated;
use KBox\RoutingHelpers;
use Carbon\Carbon;

class PublicLinksTest extends TestCase
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

    public function publiclink_create_invalid_request_data()
    {
        return [
            [[]],
            [['to_id' => false]],
            [['to_id' => null]],
            [['to_id' => 'me']],
            [['to_id' => 1, 'to_type' => 'other'], ['to_type']],
            [['slug' => 'a complete Title with some @ # and &'], ['to_id', 'to_type', 'slug']],
            [['expiration' => 'a string'], ['to_id', 'to_type', 'expiration']],
            [['expiration' => false], ['to_id', 'to_type', 'expiration']],
            [['expiration' => ''], ['to_id', 'to_type', 'expiration']],
            [['expiration' => Carbon::now()->subWeek()->toDateString()], ['to_id', 'to_type', 'expiration']],
        ];
    }

    private function createUser($capabilities, $userParams = [])
    {
        return tap(factory(User::class)->create($userParams))->addCapabilities($capabilities);
    }

    /**
     * @dataProvider publiclink_create_invalid_request_data
     */
    public function testStoreValidationIsBlockingTheRequest($data, $error_keys = null)
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);

        $params = array_merge([
            '_token' => csrf_token(),
            ], $data);

        $response = $this->actingAs($user)->json('POST', route('links.store'), $params);
        
        $response->assertStatus(422);
        $response->assertJsonStructure(is_null($error_keys) ? array_keys($data) : $error_keys);
    }

    public function testCreatePublicLinkForDocument()
    {
        $this->withKlinkAdapterFake();

        $this->expectsEvents(ShareCreated::class);

        $user = $this->createUser(Capability::$PARTNER);
        $document = $this->createDocument($user);

        $this->actingAs($user);

        $params = [
            '_token' => csrf_token(),
            'to_id' => $document->id,
            'to_type' => 'document',
            ];

        $response = $this->actingAs($user)->json('POST', route('links.store'), $params);

        $response->assertStatus(201);
        $response->assertJsonStructure([
                 'id',
                 'slug',
                 'url'
             ]);

        $share = Shared::where('shareable_id', $document->id)->sharedByMe($user)->with('sharedwith')->first();
        
        $response->assertJson([
             'url' => route('publiclinks.show', ['link' => $share->token]),
        ]);
    }

    public function testCreatePublicLinkForDocumentWithSlug()
    {
        $this->withKlinkAdapterFake();

        $this->expectsEvents(ShareCreated::class);

        $user = $this->createUser(Capability::$PARTNER);
        $document = $this->createDocument($user);

        $params = [
            '_token' => csrf_token(),
            'to_id' => $document->id,
            'to_type' => 'document',
            'slug' => 'my-slug'
            ];

        $response = $this->actingAs($user)->json('POST', route('links.store'), $params);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'slug',
            'url'
        ]);
        $response->assertJson([
            'url' => route('publiclinks.show', ['link' => 'my-slug']),
        ]);
    }

    public function testDeletePublicLink()
    {
        $share = factory(Shared::class, 'publiclink')->create();

        $user = $share->user;

        $user->addCapabilities(Capability::$PARTNER);

        $params = [
            // '_token' => csrf_token(),
            'links' => $share->sharedwith_id,
            ];

        $response = $this->actingAs($user)->json('delete', route('links.destroy', $params));
        $response->assertJson([
        'status' => 'ok',
        ]);
    }

    public function testUpdatePublicLink()
    {
        $share = factory(Shared::class, 'publiclink')->create();

        $user = $share->user;

        $user->addCapabilities(Capability::$PARTNER);

        $this->actingAs($user);

        $original_slug = $share->sharedwith->slug;
        $original_expiration = $share->expiration;
        
        // change slug

        $response = $this->actingAs($user)->json('put', route('links.update', $share->sharedwith_id), [
            'slug' => 'new-slug'
        ]);

        $response->assertJson([
            'slug' => 'new-slug',
        ]);

        // change expiration

        $new_expiration = Carbon::now()->addWeek();

        $response = $this->actingAs($user)->json('put', route('links.update', $share->sharedwith_id), [
            'expiration' => $new_expiration->toDateTimeString()
        ]);
        $response->assertJsonFragment([
            'expiration' => $new_expiration->toDateTimeString()
        ]);
    }

    public function testPublicUrlRedirectToPreview()
    {
        $share = factory(Shared::class, 'publiclink')->create();

        $url = $share->sharedwith->url;
        $expected_redirect = RoutingHelpers::preview($share->shareable);

        $response = $this->get($url);

        $response->assertLocation($expected_redirect);
    }

    private function createDocument(User $user, $visibility = 'private')
    {
        return factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'visibility' => $visibility,
        ]);
    }
}
