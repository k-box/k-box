<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivacySummaryPageControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_privacy_summary_page_is_presented_if_defined()
    {
        $url = route('privacy.summary');

        $response = $this->get($url);
        
        $response->assertStatus(200);
    }
    
    public function test_privacy_summary_page_return_404_if_not_defined()
    {
        $url = route('privacy.summary');

        $response = $this->get($url);
        
        $response->assertStatus(404);
    }
}
