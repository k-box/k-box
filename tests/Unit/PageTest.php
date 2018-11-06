<?php

namespace Tests\Unit;

use Tests\TestCase;
use KBox\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PageTest extends TestCase
{
    public function test_pages_can_be_created()
    {
        Storage::fake('app');

        $page = Page::create(['description' => 'A descriptive text', 'authors' => 1]);

        $this->assertInstanceOf(Page::class, $page);
        $this->assertFalse($page->exists());
        $this->assertTrue($page->isDirty());
        $this->assertEmpty($page->getKey());
        $this->assertEmpty($page->id);
        $this->assertEmpty($page->title);
        $this->assertEquals('A descriptive text', $page->description);
        $this->assertEquals(['1'], $page->authors);
        $this->assertInstanceOf(Carbon::class, $page->created_at);
        $this->assertInstanceOf(Carbon::class, $page->updated_at);
        $this->assertEquals($page->id, $page->getKey());
    }
    
    public function test_pages_can_be_created_and_saved()
    {
        Storage::fake('app');

        $page = Page::create([
            'id' => 'a-page',
            'title' => 'A Page',
            'description' => 'A descriptive text',
            'authors' => 1,
            'content' => '## page content'
        ]);

        $this->assertInstanceOf(Page::class, $page);
        $this->assertFalse($page->exists());
        $this->assertTrue($page->isDirty());
        $this->assertEquals('a-page', $page->getKey());
        $this->assertEquals('A Page', $page->title);
        $this->assertEquals('A descriptive text', $page->description);
        $this->assertEquals(['1'], $page->authors);
        $this->assertInstanceOf(Carbon::class, $page->created_at);
        $this->assertInstanceOf(Carbon::class, $page->updated_at);
        $this->assertEquals($page->id, $page->getKey());
        $this->assertEquals('## page content', $page->content);

        $page->save();
        
        Storage::disk('app')->assertExists('pages/a-page.en.md');

        $this->assertFalse($page->isDirty());
        $this->assertTrue($page->exists());

        $created_at = $page->created_at->format('Y-m-d H:i:s.u');
        $updated_at = $page->updated_at->format('Y-m-d H:i:s.u');
        $expected_content = <<<EOD
---
id: a-page
title: 'A Page'
description: 'A descriptive text'
authors:
    - 1
updated_at: '$updated_at'
created_at: '$created_at'
language: en
---
## page content
EOD;

        $this->assertEquals($expected_content, Storage::disk('app')->get('pages/a-page.en.md'));
    }
    
    public function test_page_key_is_title_slug()
    {
        Storage::fake('app');

        $page = Page::create(['title' => 'A Page']);

        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('a-page', $page->getKey());
        $this->assertEquals('A Page', $page->title);
    }

    public function test_created_page_can_be_deleted()
    {
        Storage::fake('app');

        $page = Page::create(['id' => 'a-page', 'title' => 'A Page', 'description' => 'A descriptive text', 'authors' => 1]);
        $page->save();

        Storage::disk('app')->assertExists('pages/a-page.en.md');
        $this->assertFalse($page->isDirty());
        $this->assertTrue($page->exists());

        $page->delete();

        Storage::disk('app')->assertMissing('pages/a-page.en.md');
        $this->assertFalse($page->isDirty());
        $this->assertFalse($page->exists());
    }

    public function test_page_can_be_instantiated_from_file()
    {
        Storage::fake('app');

        $disk = Storage::disk('app');

        $disk->makeDirectory('pages');

        $content = <<<'EOD'
---
id: a-page
language: en
title: A Page
description: A descriptive text
authors: 1
---
EOD;
        $disk->put('a-page.en.md', $content);

        $page = Page::createFromFile('a-page.en.md');

        $this->assertInstanceOf(Page::class, $page);
        $this->assertTrue($page->exists());
        $this->assertFalse($page->isDirty());
        $this->assertEquals('a-page', $page->getKey());
        $this->assertEquals('A Page', $page->title);
        $this->assertEquals('A descriptive text', $page->description);
        $this->assertEquals(['1'], $page->authors);
        $this->assertInstanceOf(Carbon::class, $page->created_at);
        $this->assertInstanceOf(Carbon::class, $page->updated_at);
    }

    public function test_all_pages_in_storage_can_be_listed()
    {
        Storage::fake('app');

        $disk = Storage::disk('app');

        $disk->makeDirectory('pages');

        $content_en = <<<'EOD'
---
id: a-page
language: en
title: A Page
description: A descriptive text
authors: 1
---
EOD;
        $content_ru = <<<'EOD'
---
id: a-page
language: ru
title: A Page
description: A descriptive text
authors: 1
---
EOD;
        $disk->put('pages/a-page.en.md', $content_en);
        $disk->put('pages/a-page.ru.md', $content_ru);

        $pages = Page::all();

        $this->assertContainsOnlyInstancesOf(Page::class, $pages);
        $this->assertCount(2, $pages);
    }

    public function test_find_single_page()
    {
        Storage::fake('app');

        $disk = Storage::disk('app');

        $disk->makeDirectory('pages');

        $content_en = <<<'EOD'
---
id: a-page
language: en
title: A Page
description: A descriptive text
authors: 1
---
EOD;
        $content_ru = <<<'EOD'
---
id: a-page
language: ru
title: A Page
description: A descriptive text
authors: 1
---
EOD;
        $disk->put('pages/a-page.en.md', $content_en);
        $disk->put('pages/a-page.ru.md', $content_ru);

        $page = Page::find('a-page', 'en');

        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('en', $page->language);
    }

    public function test_find_page()
    {
        Storage::fake('app');

        $disk = Storage::disk('app');

        $disk->makeDirectory('pages');

        $content_en = <<<'EOD'
---
id: a-page
language: en
title: A Page
description: A descriptive text
authors: 1
---
EOD;
        $content_ru = <<<'EOD'
---
id: a-page
language: ru
title: A Page
description: A descriptive text
authors: 1
---
EOD;
        $disk->put('pages/a-page.en.md', $content_en);
        $disk->put('pages/a-page.ru.md', $content_ru);

        $pages = Page::find('a-page');

        $this->assertContainsOnlyInstancesOf(Page::class, $pages);
        $this->assertCount(2, $pages);
    }
}
