<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChiperTest extends TestCase
{
    /**
     * Test the selection of the cipher based on the configured APP_KEY
     *
     * @return void
     */
    public function testCipherSelection()
    {

        $key_length = \Illuminate\Support\Str::length( config('app.key') );

        $this->assertTrue( $key_length === 16 || $key_length === 32, 'Key length not respecting the specification. Length must be 16 or 32 characters' );

        if($key_length === 16){
            $this->assertEquals( 'AES-128-CBC', config('app.cipher') );
        }
        else if($key_length === 32) {
            $this->assertEquals( 'AES-256-CBC', config('app.cipher') );
        }
        else {
            $this->fail('Key length must be 16 or 32 characters for the test to pass. Wrong configuration used');
        }
        
    }
}