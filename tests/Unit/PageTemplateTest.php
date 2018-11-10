<?php

namespace Tests\Unit;

use LogicException;
use Tests\TestCase;
use KBox\Pages\PageTemplate;

class PageTemplateTest extends TestCase
{
    public function test_all_templates_in_storage_can_be_listed()
    {
        $pages = PageTemplate::all();

        $this->assertContainsOnlyInstancesOf(PageTemplate::class, $pages);
        $this->assertCount(3, $pages);

        $pageIds = $pages->pluck('id');
        $this->assertContains(PageTemplate::PRIVACY_POLICY_LEGAL, $pageIds);
        $this->assertContains(PageTemplate::PRIVACY_POLICY_SUMMARY, $pageIds);
        $this->assertContains(PageTemplate::TERMS_OF_SERVICE, $pageIds);
    }

    public function test_find_single_template()
    {
        $page = PageTemplate::find(PageTemplate::PRIVACY_POLICY_LEGAL, 'en');

        $this->assertInstanceOf(PageTemplate::class, $page);
        $this->assertEquals(PageTemplate::PRIVACY_POLICY_LEGAL, $page->id);
        $this->assertEquals('en', $page->language);
        $this->assertNotEmpty($page->title);
        $this->assertNotEmpty($page->description);
        $this->assertEmpty($page->authors);
        $this->assertEquals('...', $page->content);
    }

    public function test_find_template()
    {
        $pages = PageTemplate::find(PageTemplate::PRIVACY_POLICY_LEGAL);

        $this->assertContainsOnlyInstancesOf(PageTemplate::class, $pages);
        $this->assertCount(1, $pages);
    }

    public function test_template_cannot_be_changed()
    {
        $this->expectException(LogicException::class);
        $page = PageTemplate::find(PageTemplate::PRIVACY_POLICY_LEGAL, 'en');
        $page->content = "change";

        $page->save();
    }
    
    public function test_template_cannot_be_deleted()
    {
        $this->expectException(LogicException::class);
        $page = PageTemplate::find(PageTemplate::PRIVACY_POLICY_LEGAL, 'en');

        $page->delete();
    }
    
    public function test_template_cannot_be_created()
    {
        $this->expectException(LogicException::class);
        $page = PageTemplate::create(['id' => 'a-page']);
    }
}
