<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use KBox\DuplicateDocument;

class DuplicateDocumentTest extends TestCase
{
    public function test_sent_return_true_when_notification_sent_at_has_a_value()
    {
        $d = (new DuplicateDocument())->forceFill([
            'notification_sent_at' => Carbon::now()
        ]);

        $this->assertTrue($d->sent);
    }

    public function test_sent_attributes_can_set_the_notification_sent_at_timestamp()
    {
        $d = new DuplicateDocument();

        $d->sent = true;

        $this->assertNotNull($d->notification_sent_at);
    }

    public function test_message_attribute_is_retrievable()
    {
        $d = new DuplicateDocument();

        $this->assertEquals('', $d->message);
    }
}
