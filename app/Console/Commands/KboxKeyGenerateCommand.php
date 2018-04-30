<?php

namespace KBox\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Console\KeyGenerateCommand;

class KboxKeyGenerateCommand extends KeyGenerateCommand
{
    const KEY_FILE = '/app_key.key';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kbox:key
                    {--show : Display the key instead of modifying files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key';

    private $disk = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->disk = Storage::disk('app');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $key = $this->generateOrLoadKey();

        if ($this->option('show')) {
            return $this->line('<comment>'.$key.'</comment>');
        }

        // Next, we will replace the application key in the environment file so it is
        // automatically setup for this instance. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (! $this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->laravel['config']['app.key'] = $key;

        $this->info("Application key [$key] set successfully.");
    }

    /**
     * Generate a new key or load it from storage
     *
     * @return string the application key
     */
    protected function generateOrLoadKey()
    {
        if (strlen($key = $this->getSavedApplicationKey()) > 0) {
            return $key;
        }

        return $this->generateRandomKey();
    }

    /**
     * Get the current application key
     *
     * @return string
     */
    protected function getCurrentKey()
    {
        return config('app.key');
    }
    
    /**
     * Check if the key length is 32 characters
     */
    protected function isKeyValid($key)
    {
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        return strlen($key) === 32;
    }

    /**
     * Check if the key is saved
     *
     * @param string $key
     * @return bool
     */
    protected function isSaved($key)
    {
        return $this->getSavedApplicationKey() === $key;
    }

    /**
     * Save the key in the storage
     *
     * @param string $key
     */
    protected function saveApplicationKey($key)
    {
        $this->disk->put(self::KEY_FILE, $key);
    }

    /**
     * Get the saved application key from storage
     *
     * @return string the stored application key if defined, an empty string if no application key was saved
     */
    protected function getSavedApplicationKey()
    {
        if (! $this->disk->exists(self::KEY_FILE)) {
            return '';
        }

        return $this->disk->get(self::KEY_FILE);
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string  $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = $this->getCurrentKey();
        
        if ($this->isKeyValid($currentKey)) {
            if (! $this->isSaved($currentKey)) {
                $this->saveApplicationKey($currentKey);
            }

            return false;
        }

        $this->writeNewEnvironmentFileWith($key);
        $this->saveApplicationKey($key);

        return true;
    }
}
