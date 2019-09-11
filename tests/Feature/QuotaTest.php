<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\Facades\Quota;
use KBox\Services\Quota as QuotaService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotaTest extends TestCase
{
    use DatabaseTransactions;

    public function quota_value_provider()
    {
        return [
            [null, INF],
            [-10, 0],
            [0, 0],
            [10, 10],
            [false, 0],
            [true, 0],
            [[], 0],
            ["a string", 0],
            ["100", 100],
            ["-100", 0],
            ["null", 0],
        ];
    }

    public function threshold_value_provider()
    {
        return [
            [null, QuotaService::DEFAULT_THRESHOLD],
            [-10, 0],
            [0, 0],
            [10, 10],
            [false, QuotaService::DEFAULT_THRESHOLD],
            [true, QuotaService::DEFAULT_THRESHOLD],
            [[], QuotaService::DEFAULT_THRESHOLD],
            ["a string", 0],
            ["100", 100],
            ["-100", 0],
            ["null", 0],
        ];
    }

    /**
     * @dataProvider quota_value_provider
     */
    public function test_limit_configuration($value, $expected)
    {
        config([
            'quota.user' => $value,
        ]);

        $this->assertEquals($expected, Quota::limit());
    }

    public function test_is_unlimited()
    {
        config([
            'quota.user' => null,
        ]);

        $this->assertTrue(Quota::isUnlimited());
    }

    public function test_not_unlimited()
    {
        config([
            'quota.user' => 10,
        ]);

        $this->assertFalse(Quota::isUnlimited());
    }
    
    /**
     * @dataProvider threshold_value_provider
     */
    public function test_threshold_is_returned($value, $expected)
    {
        config([
            'quota.threshold' => $value,
        ]);

        $this->assertEquals($expected, Quota::threshold());
    }
}
