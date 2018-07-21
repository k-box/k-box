<?php

namespace OneOffTech\VideoProcessing;

use OneOffTech\VideoProcessing\Drivers\VideoCli;

/**
 * @see \OneOffTech\VideoProcessing\LocalVideoProcessor
 */
class VideoProcessorFactory
{
    /**
     * Create a new video processor instance.
     *
     * @return \OneOffTech\VideoProcessing\Contracts\VideoProcessor
     */
    public function make()
    {
        return new LocalVideoProcessor();
    }

    public static function isInstalled()
    {
        // VideoProcessorFactory
        return VideoCli::isInstalled();
    }
}
