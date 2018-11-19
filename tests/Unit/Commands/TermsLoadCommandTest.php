<?php

namespace Tests\Unit\Commands;

use Artisan;
use Tests\TestCase;
use KBox\Pages\Page;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class TermsLoadCommandTest extends TestCase
{
    public function test_pages_are_created_from_templates_using_default_language()
    {
        Storage::fake('app');
        Event::fake();

        Storage::disk('app')->assertMissing('pages/terms.en.md');
        
        $exitCode = Artisan::call('terms:load');
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode);
        $this->assertTrue(str_contains($output, "Terms page loaded"));
        
        Storage::disk('app')->assertExists('pages/terms.en.md');

        $terms = Page::find(Page::TERMS_OF_SERVICE, 'en');

        $this->assertInstanceOf(Page::class, $terms);
    }

    public function test_pages_are_not_replaced_if_content_did_not_change()
    {
        Storage::fake('app');
        Event::fake();

        $assets = Storage::disk('assets');

        Storage::disk('app')->put('pages/terms.en.md', $assets->get('pages/stubs/terms.en.md'));
        
        $exitCode = Artisan::call('terms:load');
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode);
        $this->assertTrue(str_contains($output, "Terms already existing, nothing to do"));
        
        Storage::disk('app')->assertExists('pages/terms.en.md');

        $terms = Page::find(Page::TERMS_OF_SERVICE, 'en');

        $this->assertInstanceOf(Page::class, $terms);
    }
}
