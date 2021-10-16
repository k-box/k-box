<?php

namespace Tests\Unit;

use KBox\Flags;
use Tests\TestCase;
use Illuminate\Support\Str;

class FlagsHelpersTest extends TestCase
{
    public function valid_constant_flags_provider()
    {
        return [
            ['PLUGINS']
        ];
    }
    
    public function test_flags_helper_return_flags_instance_if_no_value_is_passed()
    {
        $ret = flags();

        $this->assertInstanceOf(Flags::class, $ret);
    }
    
    public function test_flags_helper_return_is_enabled_respose_if_key_is_passed()
    {
        $ret = flags('plugins');

        $this->assertFalse($ret);
    }

    /**
     * @dataProvider valid_constant_flags_provider
     */
    public function test_automated_is_flag_enabled_method_can_be_invoked($flag_constant_name)
    {
        $function_name = 'is'.Str::studly($flag_constant_name).'Enabled';

        $ret = flags()->{$function_name}();

        $this->assertFalse($ret);
    }
}
