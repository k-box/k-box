<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;

class OembedControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function create_document()
    {
        Storage::fake('local');
        
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);
        $document->document_uri = null;
        $document->save();

        return $document;
    }

    public function generate_invalid_urls()
    {
        return [
            ['http://localhost/hello'],
            ['http://localhost/hello/1025'],
            ['https://localhost/hello/1025'],
            ['ftp://localhost/hello/1025'],
            ['javascript://localhost/hello/1025'],
            ['javascript:void(0)'],
            ['javascript:void(0)'],
            ['http://localhost:8000/d/show/10?hello=true'],
            ['http://localhost:8000/d/show/10#something'],
            ['http://localhost:8000/d/show/10#\'DROP TABLE *'],
            ['http://localhost:8000/d/show/#\'DROP TABLE *'],
            [urlencode('http://localhost/hello')],
        ];
    }

    public function test_oembed_json_is_returned()
    {
        $document = $this->create_document();
        
        $response = $this->json('GET', 'api/oembed?format=json&url='.urlencode($document->document_uri));
        
        $response
            ->assertStatus(200)
            ->assertJson([
                'version' => '1.0',
                'type' => 'rich',
                'provider_name' => config('app.name'),
                'provider_url' => config('app.url'),
                'title' => e($document->title),
                'html' => '<iframe width="480" height="360" src="'.route('documents.embed', $document->uuid).'" class="kbox_embed_iframe" frameborder="0" allowfullscreen></iframe>'
            ])
            ->assertJsonStructure([
                "version",
                "type",
                "provider_name",
                "provider_url",
                "width",
                "height",
                "title",
                "html",
            ]);
    }

    public function test_oembed_respect_maxwith_and_height()
    {
        $document = $this->create_document();
        
        $response = $this->json('GET', 'api/oembed?format=json&maxwidth=100&maxheight=100&url='.urlencode($document->document_uri));
        
        $response
            ->assertStatus(200)
            ->assertJson([
                'version' => '1.0',
                'type' => 'rich',
                'width' => 100,
                'height' => 100,
            ])
            ->assertJsonStructure([
                "version",
                "type",
                "provider_name",
                "provider_url",
                "width",
                "height",
                "title",
                "html",
            ]);
    }
    
    public function test_oembed_return_not_implemented_for_xml_format()
    {
        $response = $this->json('GET', 'api/oembed?format=xml&url='.urlencode('http://localhost/hello'));
        
        $response->assertStatus(501);
    }
    
    /**
     * @dataProvider generate_invalid_urls
     */
    public function test_oembed_return_not_found_for_invalid_urls($url)
    {
        $response = $this->json('GET', 'api/oembed?format=json&url='.urlencode($url));
            
        $response->assertStatus(404);
    }
}
