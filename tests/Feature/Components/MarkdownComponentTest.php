<?php

namespace Tests\Feature\Components;

use Tests\TestCase;

class MarkdownComponentTest extends TestCase
{
    public function test_markdown_is_converted_to_html()
    {
        $view = $this->blade(
            '<x-markdown>## a title</x-markdown>'
        );

        $expected = <<<'html'
        <div class="prose">
            <h2>a title<a id="uc--a-title" href="#a-title" name="a-title" class="markdown-header-link" aria-hidden="true" title="Permalink">¶</a></h2>
        </div>
        html;

        $this->assertEquals($expected, trim((string)$view));
    }

    public function test_class_attribute_is_honored()
    {
        $view = $this->blade(
            '<x-markdown class="additional-class">## a title</x-markdown>'
        );

        $expected = <<<'html'
        <div class="prose additional-class">
            <h2>a title<a id="uc--a-title" href="#a-title" name="a-title" class="markdown-header-link" aria-hidden="true" title="Permalink">¶</a></h2>
        </div>
        html;

        $this->assertEquals($expected, trim((string)$view));
    }
    
    public function test_html_is_stripped()
    {
        $view = $this->blade(
            '<x-markdown><h2>a title</h2><a id="start">Hello</a></x-markdown>'
        );

        $expected = <<<'html'
        <div class="prose">
            
        </div>
        html;

        $this->assertEquals($expected, trim((string)$view));
    }
}
