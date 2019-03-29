<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_login_page_is_presented_as_default_route()
    {
        $response = $this->get('/');
        $response->assertSee('Login');
    }
}
