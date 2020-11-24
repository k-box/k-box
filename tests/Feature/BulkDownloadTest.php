<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use KBox\Capability;


class BulkDownloadTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_bulk_download_when_no_data_sent()
    {
       $this->withoutExceptionHandling();
       $user = tap(factory(\KBox\User::class)->create(), function ($u) {
        $u->addCapabilities(Capability::$PARTNER);
        });
       
       $response = $this
            ->withHeaders([
                'content-type' => 'application/json',
            ])
            ->actingAs($user)
            ->json('POST', '/documents-download/');

       $this->assertEquals(200, $response->status());
    }
    public function test_bulk_download_when_documents_sent(){
        
        $this->withoutExceptionHandling();
        $user = factory(\KBox\User::class)->create();

        $this->actingAs($user)
             ->json('POST','/documents-download/',['documents'=>[1,2,3,4]])
             ->assertStatus(200)
             ->assertJson([
                 'status'=>"download",
             ]);
    }
}
