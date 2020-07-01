<?php

namespace Tests\Unit\Commands;

use Artisan;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class KboxKeyGenerateCommandTest extends TestCase
{
    public function test_application_key_is_not_overwritten_if_defined()
    {
        $this->markTestSkipped(
            'This test is having side effects on subsequents tests as it modifies the APP_KEY in the .env file.'
        );

        Storage::fake('app');

        // generate a default key that is only available in the .env file
        // like if it was set using an environment variable during Docker startup
        Artisan::call('key:generate', []);
        $original = config('app.key');
        Storage::disk('app')->put('/app_key.key', $original);
        
        $exitCode = Artisan::call('kbox:key', []);
        
        $this->assertEquals(0, $exitCode);
        
        $applied = config('app.key');

        $this->assertEquals($original, $applied);
        $this->assertEquals($original, Storage::disk('app')->get('/app_key.key'));
    }
    
    public function test_application_key_set_from_environment_is_saved()
    {
        $this->markTestSkipped(
            'This test is having side effects on subsequents tests as it modifies the APP_KEY in the .env file.'
        );

        Storage::fake('app');

        Artisan::call('key:generate', []);
        $original = config('app.key');
        if (Storage::disk('app')->exists('/app_key.key')) {
            Storage::disk('app')->delete('/app_key.key');
        }
        
        $exitCode = Artisan::call('kbox:key', []);
        
        $this->assertEquals(0, $exitCode);
        
        $applied = config('app.key');

        $this->assertEquals($original, $applied);
        $this->assertEquals($original, Storage::disk('app')->get('/app_key.key'));
    }
    
    public function test_application_key_is_generated()
    {
        $this->markTestSkipped(
            'This test is having side effects on subsequents tests as it modifies the APP_KEY in the .env file.'
        );

        Storage::fake('app');
        
        config(['app.key' => '']);
        $original = config('app.key');
        
        $exitCode = Artisan::call('kbox:key', []);
        
        $this->assertEquals(0, $exitCode);
        
        $applied = config('app.key');

        $this->assertNotEquals($original, $applied);
        $this->assertEquals($applied, Storage::disk('app')->get('/app_key.key'));
    }
    
    public function test_application_key_is_loaded_from_stored_key_file()
    {
        $this->markTestSkipped(
            'This test is having side effects on subsequents tests as it modifies the APP_KEY in the .env file.'
        );

        Storage::fake('app');
        
        $initial = '12345678901234567890123456789012';
        // config(['app.key' => '']);
        $original = config('app.key');
        Storage::disk('app')->put('/app_key.key', $initial);
        
        $exitCode = Artisan::call('kbox:key', []);
        
        $this->assertEquals(0, $exitCode);
        
        $applied = config('app.key');

        $this->assertNotEquals($original, $applied);
        $this->assertEquals($applied, Storage::disk('app')->get('/app_key.key'));
        $this->assertEquals($initial, $applied);
    }
    
    public function test_short_application_key_is_replaced()
    {
        $this->markTestSkipped(
            'This test is having side effects on subsequents tests as it modifies the APP_KEY in the .env file.'
        );
        
        Storage::fake('app');
        
        config(['app.key' => '1234567890123456']);
        $original = config('app.key');
        
        $exitCode = Artisan::call('kbox:key', []);
        
        $this->assertEquals(0, $exitCode);
        
        $applied = config('app.key');

        $this->assertNotEquals($original, $applied);
        $this->assertEquals($applied, Storage::disk('app')->get('/app_key.key'));
    }
}
