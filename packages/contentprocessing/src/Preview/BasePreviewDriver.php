<?php

namespace KBox\Documents\Preview;

use KBox\File;
use KBox\Documents\Contracts\PreviewDriver;

abstract class BasePreviewDriver implements PreviewDriver
{
    public function isSupported(File $file)
    {
        return in_array($file->mime_type, $this->supportedMimeTypes());
    }
}
