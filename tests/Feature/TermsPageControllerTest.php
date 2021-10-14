<?php

namespace Tests\Feature;

use Markdown;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class TermsPageControllerTest extends TestCase
{
    public function test_terms_page_is_presented_if_defined()
    {
        Storage::fake('app');

        $disk = Storage::disk('app');

        $disk->makeDirectory('pages');

        $content_en = <<<'EOD'
---
id: terms
language: en
title: Terms
description: The terms description
authors: 1
---

## Content
EOD;
        $expected_content_en = Markdown::convertToHtml('## Content');

        $disk->put('pages/terms.en.md', $content_en);
        $url = route('terms');

        $response = $this->get($url);
        
        $response->assertStatus(200);
        $response->assertViewIs('static.page');
        $response->assertViewHas('pagetitle', 'Terms');
        $response->assertViewHas('pagedescription', 'The terms description');
        $response->assertViewHas('page_content', $expected_content_en);
    }

    public function test_terms_page_fallback_is_used_when_not_available_in_requested_locale()
    {
        Storage::fake('app');

        $disk = Storage::disk('app');

        $disk->makeDirectory('pages');

        $content = <<<'EOD'
---
id: terms
language: en
title: Terms
description: The terms description
authors: 1
---

## Content
EOD;
        $expected_content = Markdown::convertToHtml('## Content');

        $disk->put('pages/terms.en.md', $content);
        $url = route('terms');

        $response = $this->withHeaders([
            'ACCEPT_LANGUAGE' => 'de,en;q=0.5',
        ])->get($url);
        
        $response->assertStatus(200);
        $response->assertViewIs('static.page');
        $response->assertViewHas('pagetitle', 'Terms');
        $response->assertViewHas('pagedescription', 'The terms description');
        $response->assertViewHas('page_content', $expected_content);
    }
    
    public function test_terms_page_return_404_if_not_defined()
    {
        Storage::fake('app');

        $url = route('terms');

        $response = $this->get($url);
        
        $response->assertStatus(404);
    }
}
