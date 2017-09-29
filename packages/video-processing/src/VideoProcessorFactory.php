<?php

namespace OneOffTech\VideoProcessing;

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
}
