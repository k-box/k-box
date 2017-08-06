<?php

use Tests\BrowserKitTestCase;

class ServiceLoadTest extends BrowserKitTestCase
{
    public function testBasicExample()
    {
        $service = app(Content\Services\PreviewService::class);

        $this->assertInstanceOf(Content\Services\PreviewService::class, $service);
    }
}
