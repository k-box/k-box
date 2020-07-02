<?php

namespace Tests\Unit;

use Exception;
use Tests\TestCase;
use KBox\Contracts\Action;

class ActionTest extends TestCase
{
    public function test_action_calls_next_when_handling_exception()
    {
        (new ActionThatCanFail())->handle('something', function ($output) {
            $this->assertEquals('something', $output);
        });
    }
    
    public function test_action_can_throw_exception()
    {
        $this->expectException(Exception::class);
        (new ActionThatFail())->handle('something', function ($output) {
        });
    }
}

class ActionThatCanFail extends Action
{
    protected $canFail = true;

    public function run($something)
    {
        throw new \Exception('The exception');
    }
}

class ActionThatFail extends Action
{
    public function run($something)
    {
        throw new \Exception('The exception');
    }
}
