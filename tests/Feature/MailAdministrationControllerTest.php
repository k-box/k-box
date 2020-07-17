<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Option;
use Tests\TestCase;
use KBox\Capability;
use KBox\Mail\TestingMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MailAdministrationControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testMailIsEnabledWithLogDriver()
    {
        Option::remove('mail.host');
        Option::remove('mail.port');
        Option::remove('mail.from.address');
        Option::remove('mail.from.name');
        Option::remove('mail.username');
        Option::remove('mail.password');

        config(['mail.default' => 'log']);

        $this->assertTrue(Option::isMailEnabled());
    }
    
    public function testMailIsNotEnabledWithSmtpDriver()
    {
        $exitCode = Artisan::call('config:clear');

        // Manually resetting the configuration as on CI job seems to be needed
        config([
            'mail.mailers.smtp.host' => null,
            'mail.mailers.smtp.port' => null,
            'mail.from.address' => null,
            'mail.from.name' => null,
        ]);

        Option::remove('mail.host');
        Option::remove('mail.port');
        Option::remove('mail.from.address');
        Option::remove('mail.from.name');
        Option::remove('mail.username');
        Option::remove('mail.password');

        config(['mail.default' => 'smtp']);

        $this->assertFalse(Option::isMailEnabled());
    }
    
    public function testMailIsEnabledWithSmtpDriver()
    {
        Option::remove('mail.host');
        Option::remove('mail.port');
        Option::remove('mail.from.address');
        Option::remove('mail.from.name');
        Option::remove('mail.username');
        Option::remove('mail.password');

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.something.com',
            'mail.mailers.smtp.port' => 465,
            'mail.from.address' => 'from@k-link.technology',
            'mail.from.name' => 'Testing DMS',
        ]);

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $this->assertTrue(Option::isMailEnabled());

        $response = $this->actingAs($user)->get(route('administration.mail.index'));

        $response->assertSee('from@k-link.technology');
        $response->assertSee('Testing DMS');
        $response->assertSee('smtp.something.com');
        $response->assertSee('465');
    }

    public function testMailSettingsArePresentedWithLogDriver()
    {
        Option::remove('mail.host');
        Option::remove('mail.port');
        Option::remove('mail.from.address');
        Option::remove('mail.from.name');
        Option::remove('mail.username');
        Option::remove('mail.password');

        config([
            'mail.default' => 'log',
            'mail.from.address' => 'from@k-link.technology',
            'mail.from.name' => 'Testing DMS',
        ]);

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $this->assertTrue(Option::isMailEnabled());

        $response = $this->actingAs($user)->get(route('administration.mail.index'));

        $response->assertSee(trans('administration.mail.log_driver_used'));
        $response->assertSee('from@k-link.technology');
        $response->assertSee('Testing DMS');
    }

    public function testMailConfigurationSaved()
    {
        Option::remove('mail.host');
        Option::remove('mail.port');
        Option::remove('mail.from.address');
        Option::remove('mail.from.name');
        Option::remove('mail.username');
        Option::remove('mail.password');

        config([
            'mail.default' => 'smtp',
        ]);

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $response = $this->actingAs($user)
            ->from(route('administration.mail.index'))
            ->post(route('administration.mail.store'), [
                'from_address' => 'test@k-link.technology',
                'from_name' => 'Test DMS',
                'host' => 'smtp.example.com',
                'port' => '465',
                'smtp_u' => 'user',
                'smtp_p' => 'password',
            ]);
        $response->assertSessionHasNoErrors();

        $this->assertEquals('test@k-link.technology', Option::option('mail.from.address', false));
        $this->assertEquals('Test DMS', Option::option('mail.from.name', false));
        $this->assertEquals('465', Option::option('mail.port', 0));
        $this->assertEquals('smtp.example.com', Option::option('mail.host', false));
        $this->assertEquals('user', Option::option('mail.username', false));
        $this->assertEquals(base64_encode('password'), Option::option('mail.password', false));
        $this->assertTrue(Option::isMailEnabled());
    }

    public function testSendingTestMessage()
    {
        Mail::fake();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.something.com',
            'mail.mailers.smtp.port' => 465,
            'mail.from.address' => 'from@k-link.technology',
            'mail.from.name' => 'Testing DMS',
        ]);

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $this->assertTrue(Option::isMailEnabled());

        $response = $this->actingAs($user)->get(route('administration.mail.test'));

        $response->assertRedirect(route('administration.mail.index'));
        $response->assertSessionHas('flash_message', trans('administration.mail.test_success_msg', ['from' => config('mail.from.address')]));

        Mail::assertSent(TestingMail::class);
    }

    public function testSendingTestMessageFails()
    {
        Mail::fake();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.something.com',
            'mail.mailers.smtp.port' => 465,
            'mail.from.address' => null,
            'mail.from.name' => 'Testing DMS',
        ]);

        $adapter = $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $this->assertFalse(Option::isMailEnabled());

        $response = $this->actingAs($user)->get(route('administration.mail.test'));

        $response->assertRedirect(route('administration.mail.index'));
        $response->assertSessionHasErrors([
            'mail_send' => trans('administration.mail.test_failure_msg'),
        ]);

        Mail::assertNotSent(TestingMail::class);
    }
}
