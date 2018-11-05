<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivacyFullPageControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_privacy_full_page_is_presented_if_defined()
    {
        $url = route('privacy.full');

        $response = $this->get($url);
        
        $response->assertStatus(200);
    }
    
    public function test_privacy_full_page_return_404_if_not_defined()
    {
        $url = route('privacy.full');

        $response = $this->get($url);
        
        $response->assertStatus(404);
    }
}
