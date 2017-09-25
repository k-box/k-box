<?php

namespace Tests\Unit;

use Tests\TestCase;
use KlinkDMS\Contracts\Action;

class ActionTest extends TestCase
{
    public function test_action_calls_next_when_handling_exception()
    {
        (new ActionThatCanFail())->handle('something', function ($output) {
            $this->assertEquals('something', $output);
        });
    }
    
    /**
     * @expectedException \Exception
     */
    public function test_action_can_throw_exception()
    {
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
