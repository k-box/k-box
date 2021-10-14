<?php

namespace Tests\Unit\Commands;

use Artisan;
use KBox\Flags;
use Tests\TestCase;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FlagsCommandTest extends TestCase
{
    
    /**
     * Data provider with valid Flag names
     */
    public function valid_flags_provider()
    {
        return [
            ['plugins']
        ];
    }
    
    /**
     * Data provider for the flag constant names
     */
    public function valid_constant_flags_provider()
    {
        return [
            ['PLUGINS']
        ];
    }

    /**
     * Data provider with unacceptable Flag names
     */
    public function invalid_flags_provider()
    {
        return [
            [''],
            ['string'],
            [100],
            ['true'],
            ['false'],
            [true],
            [false],
        ];
    }

    /**
     * Test the execution with both --enable and --disable options
     */
    public function test_using_both_enable_and_disable_options_is_refused()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Option --enable and --disable cannot be used together.");

        $exitCode = Artisan::call('flags', [
            '--enable' => true,
            '--disable' => true,
            'flag' => 'plugins',
        ]);
        $output = Artisan::output();
        $this->assertEquals(0, $exitCode);
    }
    
    /**
     * @dataProvider invalid_flags_provider
     */
    public function test_invalid_flags_cannot_be_used($flag)
    {
        $this->expectException(InvalidArgumentException::class);

        $exitCode = Artisan::call('flags', [
            'flag' => $flag,
        ]);
        $output = Artisan::output();
        $this->assertEquals(0, $exitCode);
    }

    public function test_call_without_options_enables_the_flag()
    {
        $flag = 'plugins';

        $res = Artisan::call('flags', [
            'flag' => $flag
        ]);
        $output = Artisan::output();

        $this->assertTrue(Flags::isEnabled($flag));
        $this->assertFalse(Flags::isDisabled($flag));
        $this->assertTrue(Str::contains($output, 'Flag plugins enabled'));
    }
    
    public function test_flag_can_be_disabled()
    {
        $flag = 'plugins';
        Flags::enable($flag);

        $res = Artisan::call('flags', [
            'flag' => $flag,
            '--disable' => true
        ]);
        $output = Artisan::output();

        $this->assertFalse(Flags::isEnabled($flag));
        $this->assertTrue(Flags::isDisabled($flag));
        $this->assertTrue(Str::contains($output, 'Flag plugins disabled'));
    }
}
