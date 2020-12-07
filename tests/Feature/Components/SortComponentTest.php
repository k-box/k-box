<?php

namespace Tests\Feature\Components;

use KBox\Sorter;
use Tests\TestCase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

class SortComponentTest extends TestCase
{
    private function makeRequest($params)
    {
        return Request::createFromBase(
            BaseRequest::create(url('/'), 'GET', $params)
        );
    }

    public function test_component_rendered_for_document()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([]));

        $view = $this->blade(
            '<x-sort :sorter="$sorter" />',
            ['sorter' => $sorter]
        );

        $sorting_fields = [
            trans("sort.labels.update_date"),
            trans("sort.labels.creation_date"),
            trans("sort.labels.name"),
            trans("sort.labels.type"),
            trans("sort.labels.language"),
        ];

        $view->assertSeeTextInOrder($sorting_fields);
    }

    public function test_component_rendered_for_starred()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([]), 'starred');

        $view = $this->blade(
            '<x-sort :sorter="$sorter" />',
            ['sorter' => $sorter]
        );

        $sorting_fields = [
            trans("sort.labels.update_date"),
            trans("sort.labels.creation_date"),
            trans("sort.labels.name"),
            trans("sort.labels.type"),
            trans("sort.labels.language"),
        ];

        $view->assertSeeTextInOrder($sorting_fields);
    }

    public function test_component_rendered_for_shared()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([]), 'shared');

        $view = $this->blade(
            '<x-sort :sorter="$sorter" />',
            ['sorter' => $sorter]
        );

        $not_sorting_fields = [
            "sort.labels.name",
            "sort.labels.type",
            "sort.labels.language",
        ];

        $sorting_fields = [
            "sort.labels.update_date",
            "sort.labels.creation_date",
            "sort.labels.shared_by",
            "sort.labels.shared_date",
        ];

        foreach ($sorting_fields as $label) {
            $view->assertSee(trans($label));
            $view->assertDontSee($label);
        }

        foreach ($not_sorting_fields as $label) {
            $view->assertDontSee(trans($label));
        }
    }
}
