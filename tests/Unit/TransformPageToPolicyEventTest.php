<?php

namespace Tests\Unit;

use Tests\TestCase;
use KBox\Pages\Page;
use KBox\Events\PageChanged;
use KBox\Events\PrivacyPolicyUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use KBox\Listeners\TransformPageToPolicyEvent;

class TransformPageToPolicyEventTest extends TestCase
{
    private function createPrivacyLegalPage()
    {
        $page = Page::create([
            'id' => Page::PRIVACY_POLICY_LEGAL,
            'title' => 'Privacy Policy',
            'description' => 'The K-Box full privacy policy text',
            'authors' => 1,
            'content' => '## page content',
            'language' => 'en',
        ]);

        $page->save();

        return $page;
    }
    
    private function createPage()
    {
        $page = Page::create([
            'id' => 'a-page',
            'title' => 'A Page',
            'description' => 'A Page',
            'authors' => 1,
            'content' => '## page content',
            'language' => 'en',
        ]);

        $page->save();

        return $page;
    }

    public function test_privacy_updated_is_triggered()
    {
        Storage::fake('app');
        Event::fake();

        (new TransformPageToPolicyEvent())->handle(new PageChanged($this->createPrivacyLegalPage()));

        Event::assertDispatched(PrivacyPolicyUpdated::class);
    }
    
    public function test_privacy_updated_is_not_triggered()
    {
        Storage::fake('app');
        Event::fake();

        (new TransformPageToPolicyEvent())->handle(new PageChanged($this->createPage()));

        Event::assertNotDispatched(PrivacyPolicyUpdated::class);
    }
}
