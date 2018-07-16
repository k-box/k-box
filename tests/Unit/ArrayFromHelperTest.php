<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArrayFromHelperTest extends TestCase
{
    public function test_array_from_string()
    {
        $result = array_from('hello,i,am,a,string');

        $this->assertEquals(['hello','i','am','a','string'], $result);
    }
    public function test_array_from_boolean()
    {
        $result = array_from(true);

        $this->assertEquals([], $result);
    }
    public function test_array_from_number()
    {
        $result = array_from(100);

        $this->assertEquals([], $result);
    }
    public function test_array_from_null()
    {
        $result = array_from(null);

        $this->assertEquals([], $result);
    }
    public function test_array_from_object()
    {
        $result = array_from($this);

        $this->assertEquals([], $result);
    }
    public function test_array_from_array()
    {
        $result = array_from(['en', 'es']);

        $this->assertEquals(['en', 'es'], $result);
    }
}
