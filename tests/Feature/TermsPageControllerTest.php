<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TermsPageControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_terms_page_is_presented_if_defined()
    {
        $url = route('terms');

        $response = $this->get($url);
        
        $response->assertStatus(200);
    }
    
    public function test_terms_page_return_404_if_not_defined()
    {
        $url = route('terms');

        $response = $this->get($url);
        
        $response->assertStatus(404);
    }
}
