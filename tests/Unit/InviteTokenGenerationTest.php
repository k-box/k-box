<?php

namespace Tests\Unit;

use Tests\TestCase;
use KBox\InviteToken;

class InviteTokenGenerationTest extends TestCase
{
    public function test_token_is_generated()
    {
        $token = InviteToken::generate();

        $this->assertNotNull($token);
        $this->assertStringStartsWith('in', $token);
        $this->assertTrue(strlen($token) < 100);
    }
}
