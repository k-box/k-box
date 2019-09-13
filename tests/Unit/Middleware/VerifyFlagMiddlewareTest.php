<?php

namespace Tests\Unit\Middleware;

use KBox\Flags;
use Tests\TestCase;
use Illuminate\Http\Request;
use KBox\Http\Middleware\VerifyFlag;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VerifyFlagMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function test_request_denied_if_flag_disabled()
    {
        Flags::disable(Flags::PLUGINS);

        $request = new Request;

        $middleware = new VerifyFlag();

        $next = new class {
            // anonymous invokable class to track a state for the next closure
            public $called = false;

            public function __invoke($args)
            {
                $this->called = true;
                return $args;
            }
        };

        $response = TestResponse::fromBaseResponse($middleware->handle($request, $next, Flags::PLUGINS));

        $this->assertFalse($next->called);
        $response->assertStatus(200);
        $response->assertViewIs('errors.404');
    }

    public function test_request_accepted_if_flag_enabled()
    {
        Flags::enable(Flags::PLUGINS);

        $request = new Request;

        $middleware = new VerifyFlag();

        $next = new class {
            // anonymous invokable class to track a state for the next closure
            public $called = false;

            public function __invoke($args)
            {
                $this->called = true;
                return $args;
            }
        };

        $response = $middleware->handle($request, $next, Flags::PLUGINS);

        $this->assertTrue($next->called);
        $this->assertSame($response, $request);
    }
}
