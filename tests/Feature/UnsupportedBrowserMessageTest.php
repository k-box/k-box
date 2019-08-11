<?php

namespace Tests\Feature;

use Tests\TestCase;

class UnsupportedBrowserMessageTest extends TestCase
{
    public function test_unsupported_browser_page_loads()
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)'
        ])->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('errors.unsupported-browser');
    }
}
