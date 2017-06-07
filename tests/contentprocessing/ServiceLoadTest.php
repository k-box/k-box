<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceLoadTest extends TestCase
{
    
    public function testBasicExample()
    {
        $service = app(Content\Services\PreviewService::class);

        $this->assertInstanceOf(Content\Services\PreviewService::class, $service);
    }
    
}
