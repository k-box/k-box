<?php

namespace Tests\Unit;

use KBox\Upload;
use Tests\TestCase;
use KBox\GetMaximumUploadSize;

class MaximumUploadSizeTest extends TestCase
{
    public function test_maximum_upload_size_is_returned_as_integer()
    {
        config([
            'dms.max_upload_size' => "100"
        ]);

        $maximum = Upload::maximum();
        
        $this->assertEquals(100, $maximum);
    }
    
    public function test_boolean_as_maximum_upload_size_is_returned_as_integer()
    {
        config([
            'dms.max_upload_size' => false
        ]);

        $maximum = Upload::maximum();
        
        $this->assertEquals(0, $maximum);
    }
    
    public function test_negative_maximum_upload_size_value()
    {
        config([
            'dms.max_upload_size' => -100
        ]);

        $maximum = Upload::maximum();
        
        $this->assertEquals(0, $maximum);
    }
    
    public function test_maximum_upload_size_value_as_kilobytes()
    {
        config([
            'dms.max_upload_size' => 1024
        ]);

        $maximum = Upload::maximumAsKB();
        
        $this->assertEquals(1.0, $maximum);
    }
    
    public function test_get_maximum_upload_size_is_mockable()
    {
        $this->app->instance(GetMaximumUploadSize::class, function () {
            return 223;
        });

        $maximum = Upload::maximum();
        
        $this->assertEquals(223, $maximum);
    }
}
