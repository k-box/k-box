<?php

namespace Tests\Feature\Components;

use Tests\TestCase;
use KBox\Sorter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

class ColumnHeaderComponentTest extends TestCase
{
    private function makeRequest($params)
    {
        return Request::createFromBase(
            BaseRequest::create(url('/'), 'GET', $params)
        );
    }
    
    public function test_renders_without_sorting_options()
    {
        $view = $this->blade(
            '<x-column-header class="text-green-500">
            {{trans("documents.descriptor.language")}}
            </x-column-header>'
        );

        $view->assertSee(trans("documents.descriptor.language"));
        $view->assertDontSee(trans('sort.change_direction'));
    }
    
    public function test_renders_current_desc_sorting_options()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([]));

        $view = $this->blade(
            '<x-column-header class="text-green-500" key="update_date" :sort="$sorter">
            {{trans("documents.descriptor.language")}}
            </x-column-header>',
            [
                'sorter' => $sorter
            ]
        );

        $view->assertSee(trans("documents.descriptor.language"));
        $view->assertSee(trans('sort.change_direction'));
        $view->assertSee(materialicon('navigation', 'arrow_upward', 'fill-current text-gray-400 w-4 h-4'));
    }
    
    public function test_renders_current_asc_sorting()
    {
        $sorter = Sorter::fromRequest($this->makeRequest(['o' => 'a']));

        $view = $this->blade(
            '<x-column-header class="text-green-500" key="update_date" :sort="$sorter">
            {{trans("documents.descriptor.language")}}
            </x-column-header>',
            [
                'sorter' => $sorter
            ]
        );

        $view->assertSee(trans("documents.descriptor.language"));
        $view->assertSee(trans('sort.change_direction'));
        $view->assertSee(materialicon('navigation', 'arrow_downward', 'fill-current text-gray-400 w-4 h-4'));
    }
    
    public function test_current_direction_not_rendered_if_different_column()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([]));

        $view = $this->blade(
            '<x-column-header class="text-green-500" key="language" :sort="$sorter">
            {{trans("documents.descriptor.language")}}
            </x-column-header>',
            [
                'sorter' => $sorter
            ]
        );

        $view->assertSee(trans("documents.descriptor.language"));
        $view->assertSee(trans('sort.change_direction'));
        $view->assertDontSee(materialicon('navigation', 'arrow_upward', 'fill-current text-gray-400 w-4 h-4'));
    }
}
