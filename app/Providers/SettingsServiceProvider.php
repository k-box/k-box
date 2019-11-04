<?php

namespace KBox\Providers;

use Illuminate\Support\ServiceProvider;
use KBox\Option;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;

class SettingsServiceProvider extends ServiceProvider
{

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
            $original = config('mail');

            $sections = Option::section('mail')->get(['key', 'value']);

            if (! $sections->isEmpty()) {
                $flat = $sections->toArray();

                $keys = Arr::pluck($flat, 'key');
                $values = Arr::pluck($flat, 'value');

                $non_flat = [];
                foreach (array_combine($keys, $values) as $key => $value) {
                    Arr::set($non_flat, $key, $value);
                }

                if (array_key_exists('mail', $non_flat)) {
                    if (isset($non_flat['mail']['password']) && ! empty($non_flat['mail']['password'])) {
                        $non_flat['mail']['password'] = base64_decode($non_flat['mail']['password']);
                    }

                    config(['mail' => array_merge($original, $non_flat['mail'])]);
                }
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
