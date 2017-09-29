<?php


namespace OneOffTech\VideoProcessing\Contracts;

interface VideoProcessor
{
    /**
     * Get the details/metadata of a video file
     *
     * @param string $file The path of the video file
     * @param array $options Additional parameters
     * @return mixed
     */
    public function details($file, $options = []);

    /**
     * Generate the thumbnail of video file
     *
     * @param string $file The path of the video file
     * @param array $options Additional parameters
     * @return mixed
     */
    public function thumbnail($file, $options = []);

    /**
     * Prepare the video file for streaming purposes
     *
     * @param string $file The path of the video file
     * @param array $options Additional parameters
     * @return mixed
     */
    public function streamify($file, $options = []);
}
