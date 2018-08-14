<?php

use Tests\BrowserKitTestCase;

class ServiceLoadTest extends BrowserKitTestCase
{
    public function testBasicExample()
    {
        $service = app(KBox\Documents\Services\PreviewService::class);

        $this->assertInstanceOf(KBox\Documents\Services\PreviewService::class, $service);
    }
}
