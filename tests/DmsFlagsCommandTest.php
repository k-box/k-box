<?php


use Illuminate\Support\Facades\Artisan;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KBox\Console\Commands\DmsFlagsCommand;

use KBox\Traits\RunCommand;

use KBox\Flags;

/*
 * Test the KBox\Console\Commands\DmsFlagsCommand artisan console command
*/
class DmsFlagsCommandTest extends BrowserKitTestCase
{
    use DatabaseTransactions, RunCommand;

    /**
     * Data provider with valid Flag names
     */
    public function valid_flags_provider()
    {
        return [
            ['unifiedsearch']
        ];
    }
    
    /**
     * Data provider for the flag constant names
     */
    public function valid_constant_flags_provider()
    {
        return [
            ['UNIFIED_SEARCH']
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
     * @expectedException     InvalidArgumentException
     * @expectedExceptionMessage Option --enable and --disable cannot be used together.
     */
    public function testExecutionWithEnableAndDisable()
    {
        $command = new DmsFlagsCommand();
        
        $res = $this->runArtisanCommand($command, [
            '--enable' => true,
            '--disable' => true,
            'flag' => 'unifiedsearch',
        ]);
    }
    
    /**
     * @dataProvider invalid_flags_provider
     * @expectedException     InvalidArgumentException
     */
    public function testExecutionWithInvalidFlagNames($flag)
    {
        $command = new DmsFlagsCommand();
        
        $res = $this->runArtisanCommand($command, [
            'flag' => $flag,
        ]);
    }

    /**
     * @dataProvider valid_flags_provider
     */
    public function testExecutionWithoutOptions($flag)
    {
        $command = new DmsFlagsCommand();
        
        // assume disabled, after first run must be enabled

        $this->assertTrue(Flags::isDisabled($flag));

        $res = $this->runArtisanCommand($command, [
            'flag' => $flag,
        ]);

        $this->assertTrue(Flags::isEnabled($flag));

        // after second run must be disabled

        $res = $this->runArtisanCommand($command, [
            'flag' => $flag,
        ]);

        $this->assertTrue(Flags::isDisabled($flag));
    }

    /**
     * @dataProvider valid_flags_provider
     */
    public function testExecutionWithEnableOption($flag)
    {
        $command = new DmsFlagsCommand();

        $res = $this->runArtisanCommand($command, [
            'flag' => $flag,
            '--enable' => true
        ]);

        $this->assertTrue(Flags::isEnabled($flag));
        $this->assertFalse(Flags::isDisabled($flag));
    }
    
    /**
     * @dataProvider valid_flags_provider
     */
    public function testExecutionWithDisableOption($flag)
    {
        $command = new DmsFlagsCommand();

        $res = $this->runArtisanCommand($command, [
            'flag' => $flag,
            '--disable' => true
        ]);

        $this->assertFalse(Flags::isEnabled($flag));
        $this->assertTrue(Flags::isDisabled($flag));
    }

    public function testFlagsHelperReturnValue()
    {
        $this->assertTrue(function_exists('flags'));

        $ret = flags();

        $this->assertInstanceOf(Flags::class, $ret);
    }

    /**
     * @dataProvider valid_constant_flags_provider
     */
    public function testFlagsHelperMagicIsEnabled($flag_constant_name)
    {
        $function_name = 'is'.studly_case($flag_constant_name).'Enabled';

        $ret = flags()->{$function_name}();

        $this->assertTrue($ret);
    }
}
