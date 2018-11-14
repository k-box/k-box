<?php

namespace Tests\Unit\Commands;

use Artisan;
use Tests\TestCase;
use KBox\Pages\Page;
use KBox\Events\PageChanged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class PrivacyLoadCommandTest extends TestCase
{
    public function test_pages_are_created_from_templates_using_default_language()
    {
        Storage::fake('app');
        Event::fake();

        Storage::disk('app')->assertMissing('pages/privacy-legal.en.md');
        Storage::disk('app')->assertMissing('pages/privacy-summary.en.md');
        
        $exitCode = Artisan::call('privacy:load');
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode);
        $this->assertTrue(str_contains($output, "Privacy policy loaded"));
        
        Storage::disk('app')->assertExists('pages/privacy-legal.en.md');
        Storage::disk('app')->assertExists('pages/privacy-summary.en.md');

        $legal = Page::find(Page::PRIVACY_POLICY_LEGAL, 'en');
        $summary = Page::find(Page::PRIVACY_POLICY_SUMMARY, 'en');

        $this->assertInstanceOf(Page::class, $legal);
        $this->assertInstanceOf(Page::class, $summary);

        Event::assertDispatched(PageChanged::class, function ($e) use ($legal) {
            return $e->page->id === $legal->id;
        });
    }

    public function test_pages_are_not_replaced_if_content_did_not_change()
    {
        Storage::fake('app');
        Event::fake();

        $assets = Storage::disk('assets');

        Storage::disk('app')->put('pages/privacy-legal.en.md', $assets->get('pages/stubs/privacy-legal.en.md'));
        Storage::disk('app')->put('pages/privacy-summary.en.md', $assets->get('pages/stubs/privacy-summary.en.md'));
        
        $exitCode = Artisan::call('privacy:load');
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode);
        $this->assertTrue(str_contains($output, "Privacy policy already existing, nothing to do"));
        
        Storage::disk('app')->assertExists('pages/privacy-legal.en.md');
        Storage::disk('app')->assertExists('pages/privacy-summary.en.md');

        $legal = Page::find(Page::PRIVACY_POLICY_LEGAL, 'en');
        $summary = Page::find(Page::PRIVACY_POLICY_SUMMARY, 'en');

        $this->assertInstanceOf(Page::class, $legal);
        $this->assertInstanceOf(Page::class, $summary);

        Event::assertNotDispatched(PageChanged::class);
    }
}
