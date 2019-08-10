<?php

namespace KBox;

class Upload
{
    /**
     * Get the maximum allowed file size in bytes
     *
     * @return int the maximum allowed file size in bytes
     */
    public static function maximum()
    {
        return app(GetMaximumUploadSize::class)->__invoke();
    }

    /**
     * Get the maximum allowed file size in KiloBytes
     *
     * @return float the maximum allowed file size in KiloBytes
     */
    public static function maximumAsKB()
    {
        return static::maximum() / 1024;
    }
}
