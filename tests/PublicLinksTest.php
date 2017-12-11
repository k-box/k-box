<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KBox\Capability;
use KBox\Shared;
use Laracasts\TestDummy\Factory;

class PublicLinksTest extends BrowserKitTestCase
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
            [['expiration' => \Carbon\Carbon::now()->subWeek()->toDateString()], ['to_id', 'to_type', 'expiration']],
        ];
    }

    /**
     * @dataProvider publiclink_create_invalid_request_data
     */
    public function testStoreValidationIsBlockingTheRequest($data, $error_keys = null)
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);

        \Session::start();
        $this->actingAs($user);

        $params = array_merge([
            '_token' => csrf_token(),
            ], $data);

        $this->json('POST', route('links.store'), $params)->assertResponseStatus(422);

        $this->seeJsonStructure(is_null($error_keys) ? array_keys($data) : $error_keys);
    }

    public function testCreatePublicLinkForDocument()
    {
        $this->withKlinkAdapterFake();

        $this->expectsEvents(KBox\Events\ShareCreated::class);

        $user = $this->createUser(Capability::$PARTNER);
        $document = $this->createDocument($user);

        \Session::start();
        $this->actingAs($user);

        $params = [
            '_token' => csrf_token(),
            'to_id' => $document->id,
            'to_type' => 'document',
            ];

        $this->json('POST', route('links.store'), $params);

        $this->assertResponseStatus(201)
             ->seeJsonStructure([
                 'id',
                 'slug',
                 'url'
             ]);

        $share = Shared::where('shareable_id', $document->id)->sharedByMe($user)->with('sharedwith')->first();
        
        $this->seeJson([
             'url' => route('publiclinks.show', ['link' => $share->token]),
        ]);
    }

    public function testCreatePublicLinkForDocumentWithSlug()
    {
        $this->withKlinkAdapterFake();

        $this->expectsEvents(KBox\Events\ShareCreated::class);

        $user = $this->createUser(Capability::$PARTNER);
        $document = $this->createDocument($user);

        \Session::start();
        $this->actingAs($user);

        $params = [
            '_token' => csrf_token(),
            'to_id' => $document->id,
            'to_type' => 'document',
            'slug' => 'my-slug'
            ];

        $this->json('POST', route('links.store'), $params);

        $this->assertResponseStatus(201)
             ->seeJsonStructure([
                 'id',
                 'slug',
                 'url'
             ])
             ->seeJson([
                'url' => route('publiclinks.show', ['link' => 'my-slug']),
            ]);
    }

    public function testDeletePublicLink()
    {
        $share = factory(Shared::class, 'publiclink')->create();

        $user = $share->user;

        $user->addCapabilities(Capability::$PARTNER);

        \Session::start();
        $this->actingAs($user);

        $params = [
            // '_token' => csrf_token(),
            'links' => $share->sharedwith_id,
            ];

        $this->json('delete', route('links.destroy', $params))
             ->seeJson([
                'status' => 'ok',
             ]);
    }

    public function testUpdatePublicLink()
    {
        $share = factory(Shared::class, 'publiclink')->create();

        $user = $share->user;

        $user->addCapabilities(Capability::$PARTNER);

        \Session::start();
        $this->actingAs($user);

        $original_slug = $share->sharedwith->slug;
        $original_expiration = $share->expiration;
        
        // change slug

        $this->json('put', route('links.update', $share->sharedwith_id), [
                'slug' => 'new-slug'
             ])
             ->seeJson([
                'slug' => 'new-slug',
             ]);

        // change expiration

        $new_expiration = \Carbon\Carbon::now()->addWeek();

        $this->json('put', route('links.update', $share->sharedwith_id), [
                'expiration' => $new_expiration->toDateTimeString()
             ])
             ->seeJson([
                    'expiration' => $new_expiration->toDateTimeString()
             ]);
    }

    public function testPublicUrlRedirectToPreview()
    {
        $share = factory(Shared::class, 'publiclink')->create();

        $url = $share->sharedwith->url;
        $expected_redirect = \KBox\RoutingHelpers::preview($share->shareable);

        \Session::start();

        // assert redirect to klink/localdocumentid/preview

        $this->visit($url);

        // dump($this->response->original->view);

        $this->assertViewName('documents.preview');
        $this->assertViewHas('document');
        $this->assertViewHas('file');
        $this->assertViewHas('body_classes');
        $this->assertViewHas('pagetitle',
            trans('documents.preview.page_title', ['document' => $share->shareable->title]));
    }
}
