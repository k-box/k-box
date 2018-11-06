<?php

namespace Tests\Feature;

use Markdown;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivacyFullPageControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_privacy_page_is_presented_if_defined()
    {
        Storage::fake('app');

        $disk = Storage::disk('app');

        $disk->makeDirectory('pages');

        $content_en = <<<'EOD'
---
id: privacy-full
language: en
title: privacy
description: The privacy description
authors: 1
---

## Content
EOD;
        $expected_content_en = Markdown::convertToHtml('## Content');

        $disk->put('pages/privacy-full.en.md', $content_en);
        $url = route('privacy.full');

        $response = $this->get($url);
        
        $response->assertStatus(200);
        $response->assertViewIs('static.page');
        $response->assertViewHas('pagetitle', 'privacy');
        $response->assertViewHas('pagedescription', 'The privacy description');
        $response->assertViewHas('page_content', $expected_content_en);
    }

    public function test_privacy_page_fallback_is_used_when_not_available_in_requested_locale()
    {
        Storage::fake('app');

        $disk = Storage::disk('app');

        $disk->makeDirectory('pages');

        $content = <<<'EOD'
---
id: privacy-full
language: en
title: privacy
description: The privacy description
authors: 1
---

## Content
EOD;
        $expected_content = Markdown::convertToHtml('## Content');

        $disk->put('pages/privacy-full.en.md', $content);
        $url = route('privacy.full');

        $response = $this->withHeaders([
            'ACCEPT_LANGUAGE' => 'de,en;q=0.5',
        ])->get($url);
        
        $response->assertStatus(200);
        $response->assertViewIs('static.page');
        $response->assertViewHas('pagetitle', 'privacy');
        $response->assertViewHas('pagedescription', 'The privacy description');
        $response->assertViewHas('page_content', $expected_content);
    }
    
    public function test_privacy_page_return_404_if_not_defined()
    {
        Storage::fake('app');

        $url = route('privacy.full');

        $response = $this->get($url);
        
        $response->assertStatus(404);
    }
}
