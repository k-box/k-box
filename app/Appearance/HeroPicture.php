<?php

namespace KBox\Appearance;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HeroPicture
{
    private const DISK = 'public';
    private const FOLDER = 'appearance';

    private $picture;

    private $filename;

    /**
     * @param string $picture
     */
    public function __construct($picture)
    {
        $this->picture = $picture;
        $this->filename = hash('sha256', $picture).'.jpg';
    }

    /**
     * @return bool
     */
    public function isLocal()
    {
        if (Str::startsWith($this->picture, url('')) || ! Str::startsWith($this->picture, 'http')) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function name()
    {
        return static::FOLDER."/$this->filename";
    }

    /**
     * Get the url of the picture
     *
     * @return string|null
     */
    public function url()
    {
        if (is_null($this->picture)) {
            return null;
        }

        if ($this->isLocal()) {
            return url($this->picture);
        }

        return url(Storage::disk(static::DISK)->url($this->name()));
    }

    /**
     * Get the local file for the specified picture
     *
     * @return string
     */
    public function path()
    {
        if ($this->isLocal()) {
            return $this->picture;
        }

        return Storage::disk(static::DISK)->path($this->name());
    }

    /**
     * @return bool
     */
    public function exists()
    {
        if ($this->isLocal()) {
            return true;
        }

        return Storage::disk(static::DISK)->exists($this->name());
    }

    /**
     * @param bool $force
     */
    public function fetch($force = false)
    {
        if (is_null($this->picture)) {
            return;
        }

        if ($this->isLocal()) {
            return;
        }

        if ($this->exists() && ! $force) {
            return;
        }

        $this->ensureAppearanceFolderExists();

        $temp_file = tempnam(sys_get_temp_dir(), 'app');

        try {
            $response = Http::sink($temp_file)->get($this->picture);
            
            if ($response->failed()) {
                logs()->warning("Appearance picture failed to download [{$response->status()}: $this->picture].");
                return null;
            }

            copy($temp_file, $this->path());
        } catch (Throwable $th) {
            logs()->warning("Appearance picture failed to download [{$th->getMessage()}: $this->picture].");
            return null;
        } finally {
            @unlink($temp_file);
        }
    }

    protected function ensureAppearanceFolderExists()
    {
        if (Storage::disk(static::DISK)->exists(static::FOLDER)) {
            return true;
        }

        return Storage::disk(static::DISK)->makeDirectory(static::FOLDER);
    }
}
