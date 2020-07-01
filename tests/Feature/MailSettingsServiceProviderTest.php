<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Option;
use KBox\Providers\SettingsServiceProvider;
use Tests\Concerns\ClearDatabase;

class MailSettingsServiceProviderTest extends TestCase
{
    use DatabaseTransactions, ClearDatabase;
    
    public function test_settings_provider_use_configuration_persisted_in_database()
    {
        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => null,
            'mail.mailers.smtp.port' => null,
            'mail.from.address' => null,
            'mail.from.name' => null,
        ]);

        $configs = $this->saveMailOptionsConfiguration([
            'mail.from.address' => 'test@k-link.technology',
            'mail.from.name' => 'Test DMS',
            'mail.port' => '465',
            'mail.host' => 'smtp.example.com',
            'mail.username' => 'user',
            'mail.password' => base64_encode('password'),
        ]);

        $provider = new SettingsServiceProvider(app());

        $this->invokePrivateMethod($provider, 'loadMailConfiguration');

        $this->assertEquals('smtp', config('mail.default'));
        $this->assertEquals($configs['mail.username'], config('mail.mailers.smtp.username'));
        $this->assertEquals(base64_decode($configs['mail.password']), config('mail.mailers.smtp.password'));
        $this->assertEquals($configs['mail.host'], config('mail.mailers.smtp.host'));
        $this->assertEquals($configs['mail.port'], config('mail.mailers.smtp.port'));
        $this->assertEquals($configs['mail.from.address'], config('mail.from.address'));
        $this->assertEquals($configs['mail.from.name'], config('mail.from.name'));
    }
    
    public function test_settings_provider_merges_mail_configuration()
    {
        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => 'smtp.example.com',
            'mail.mailers.smtp.port' => '465',
            'mail.from.address' => null,
            'mail.from.name' => null,
        ]);

        $configs = $this->saveMailOptionsConfiguration([
            'mail.from.address' => 'test@k-link.technology',
            'mail.from.name' => 'Test DMS',
            'mail.username' => 'user',
            'mail.password' => base64_encode('password'),
        ]);

        $provider = new SettingsServiceProvider(app());

        $this->invokePrivateMethod($provider, 'loadMailConfiguration');

        $this->assertEquals('smtp', config('mail.default'));
        $this->assertEquals($configs['mail.username'], config('mail.mailers.smtp.username'));
        $this->assertEquals(base64_decode($configs['mail.password']), config('mail.mailers.smtp.password'));
        $this->assertEquals('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertEquals('465', config('mail.mailers.smtp.port'));
        $this->assertEquals($configs['mail.from.address'], config('mail.from.address'));
        $this->assertEquals($configs['mail.from.name'], config('mail.from.name'));
    }
    
    public function test_settings_provider_ignores_empty_mail_configuration()
    {
        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => 'smtp.example.com',
            'mail.mailers.smtp.port' => '465',
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'mail.from.address' => null,
            'mail.from.name' => null,
        ]);

        $configs = $this->saveMailOptionsConfiguration([
            'mail.from.address' => null,
            'mail.from.name' => null,
            'mail.username' => null,
            'mail.password' => null,
        ]);

        $provider = new SettingsServiceProvider(app());

        $this->invokePrivateMethod($provider, 'loadMailConfiguration');

        $this->assertEquals('log', config('mail.default'));
        $this->assertNull(config('mail.mailers.smtp.username'));
        $this->assertNull(config('mail.mailers.smtp.password'));
        $this->assertEquals('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertEquals('465', config('mail.mailers.smtp.port'));
        $this->assertNull(config('mail.from.address'));
        $this->assertNull(config('mail.from.name'));
    }

    private function saveMailOptionsConfiguration(array $options)
    {
        foreach ($options as $name => $value) {
            Option::create(['key' => $name, 'value' => $value]);
        }

        return $options;
    }
}
