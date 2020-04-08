<?php

namespace KBox\Documents\Thumbnail;

use KBox\File;
use Illuminate\Support\Str;
use KBox\Documents\DocumentType;
use KBox\Documents\Contracts\ThumbnailGenerator;
use OneOffTech\VideoProcessing\VideoProcessorFactory;

/**
 * Video Thumbnail Generator
 *
 * Generate thumbnail of video files
 */
class VideoThumbnailGenerator implements ThumbnailGenerator
{
    public function generate(File $file) : ThumbnailImage
    {
        $videoProcessor = app()->make(VideoProcessorFactory::class)->make();
                
        $out = $videoProcessor->thumbnail($file->absolute_path);
                
        $thumb_path = str_finish(dirname($file->absolute_path), '/').Str::replaceLast('.mp4', '.png', basename($file->absolute_path));

        return ThumbnailImage::load($thumb_path)->widen(ThumbnailImage::DEFAULT_WIDTH);
    }

    public function isSupported(File $file)
    {
        return in_array($file->mime_type, $this->supportedMimeTypes()) && $file->document_type === DocumentType::VIDEO;
    }

    public function supportedMimeTypes()
    {
        return [
            'video/mp4',
        ];
    }
}
