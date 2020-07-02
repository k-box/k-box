<?php

namespace KBox\Providers;

use Illuminate\Support\ServiceProvider;
use KBox\Option;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;

class SettingsServiceProvider extends ServiceProvider
{

    /**
     * Mapping from database to configuration keys
     *
     * @var array
     */
    private $mailMappings = [
        'mail.from.address' => 'mail.from.address',
        'mail.from.name' => 'mail.from.name',
        'mail.port' => 'mail.mailers.smtp.port',
        'mail.host' => 'mail.mailers.smtp.host',
        'mail.username' => 'mail.mailers.smtp.username',
        'mail.password' => 'mail.mailers.smtp.password',
    ];
    
    /**
     * Mapping between absolute configuration keys
     * and smtp relative configuration keys
     *
     * @var array
     */
    private $smtpMappings = [
        'mail.mailers.smtp.port' => 'port',
        'mail.mailers.smtp.host' => 'host',
        'mail.mailers.smtp.username' => 'username',
        'mail.mailers.smtp.password' => 'password',
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMailConfiguration();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Loads the mail configuration from the database, if exists and attempt to override the
     * default configuration stored in the mail.php configuration file
     */
    private function loadMailConfiguration()
    {
        try {
            $mailer = config('mail.default');
            $smtpMailerConfiguration = Arr::dot(config('mail.mailers.smtp') ?? []);

            $sections = Option::sectionAsArray('mail');

            if (! empty($sections)) {
                $dottedSections = Arr::dot($sections);

                foreach ($this->mailMappings as $optionKey => $configKey) {
                    $value = $dottedSections[$optionKey] ?? $smtpMailerConfiguration[$this->smtpMappings[$configKey]];

                    if ($optionKey == 'mail.password') {
                        $value = base64_decode($value);
                    }
                    
                    $smtpMailerConfiguration[$configKey] = $value;
                }
                if ($mailer !== 'smtp') {
                    config(['mail.default' => 'smtp']);
                }
                config($smtpMailerConfiguration);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            \Log::warning('Settings Service Provider query exception', ['error' => $qe]);
        } catch (DecryptException $qe) {
            \Log::error('Settings Service Provider decrypting stored data exception', ['error' => $qe]);
        } catch (\Exception $qe) {
            \Log::error('Settings Service Provider exception', ['error' => $qe]);
        }
    }
}
