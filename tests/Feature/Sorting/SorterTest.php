<?php

namespace Tests\Feature\Sorting;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Http\Request;
use KBox\Sorter;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

class SorterTest extends TestCase
{
    use DatabaseTransactions;

    private function makeRequest($params)
    {
        return Request::createFromBase(
            BaseRequest::create(url('/'), 'GET', $params)
        );
    }

    public function test_default_parameter_are_honored()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([]));

        $this->assertEquals('updated_at', $sorter->column);
        $this->assertEquals('d', $sorter->direction);
        $this->assertEquals('DESC', $sorter->order);
        $this->assertEquals('date', $sorter->type);
        $this->assertEquals('update_date', $sorter->field);
        $this->assertTrue($sorter->isDesc());
        $this->assertFalse($sorter->isAsc());
    }

    public function test_descending_order_respected()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'update_date',
            'o' => 'd',
        ]));

        $this->assertEquals('updated_at', $sorter->column);
        $this->assertEquals('d', $sorter->direction);
        $this->assertTrue($sorter->isDesc());
        $this->assertFalse($sorter->isAsc());
        $this->assertEquals('DESC', $sorter->order);
        $this->assertEquals('date', $sorter->type);
        $this->assertEquals('update_date', $sorter->field);
    }

    public function test_wrong_order_handled_with_default()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'update_date',
            'o' => 'jump',
        ]));

        $this->assertEquals('updated_at', $sorter->column);
        $this->assertEquals('d', $sorter->direction);
        $this->assertEquals('DESC', $sorter->order);
        $this->assertEquals('date', $sorter->type);
        $this->assertEquals('update_date', $sorter->field);
    }

    public function test_wrong_field_handled_with_default()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'mime_type',
        ]));

        $this->assertEquals('updated_at', $sorter->column);
        $this->assertEquals('d', $sorter->direction);
        $this->assertEquals('DESC', $sorter->order);
        $this->assertEquals('date', $sorter->type);
        $this->assertEquals('update_date', $sorter->field);
    }

    public function field_data()
    {
        return [
            ['update_date', 'updated_at', 'date'],
            ['creation_date', 'created_at', 'date'],
            ['name', 'title', 'string'],
            ['type', 'document_type', 'string'],
            ['language', 'language', 'string'],
        ];
    }

    /**
     * @dataProvider field_data
     */
    public function test_field_to_column_conversion($field, $expected_column, $expected_type)
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => $field,
        ]));

        $this->assertEquals($expected_column, $sorter->column);
        $this->assertEquals($expected_type, $sorter->type($field));
        $this->assertEquals($field, $sorter->field);
    }

    public function test_current_identifies_current_field()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'update_date',
        ]));

        $this->assertTrue($sorter->current('update_date'));
    }

    public function test_non_current_field()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'update_date',
        ]));

        $this->assertFalse($sorter->current('name'));
    }

    public function test_sorting_url_can_generated()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'update_date',
        ]));

        $this->assertEquals('http://localhost?sc=name&o=d', $sorter->url([
            'sc' => 'name',
            'o' => 'd',
        ]));
    }

    public function test_empty_field_is_not_sortable()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'update_date',
        ]));

        $this->assertFalse($sorter->isSortable(''));
    }

    public function test_field_is_sortable()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'update_date',
        ]));

        $this->assertTrue($sorter->isSortable('name'));
    }

    public function test_field_is_not_sortable()
    {
        $sorter = Sorter::fromRequest($this->makeRequest([
            'sc' => 'update_date',
        ]));

        $this->assertFalse($sorter->isSortable('mime_type'));
    }
}
