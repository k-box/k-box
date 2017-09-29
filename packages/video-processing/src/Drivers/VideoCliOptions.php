<?php

namespace OneOffTech\VideoProcessing\Drivers;

class VideoCliOptions
{
    /**
     * The video input file
     *
     * @var string
     */
    public $source = null;

    /**
     * The command to execute
     *
     * @var string
     */
    public $command = null;
    
    public $arguments = null;

    public $options = null;

    /**
     * Create a new Video CLI options instance.
     *
     * @param string $command
     * @param string $source
     * @return void
     */
    public function __construct($command, $source, array $arguments = [], array $options = [])
    {
        $this->source = $source;
        $this->command = $command;
        $this->arguments = $arguments;
        $this->options = $options;
    }

    public function toWorkerArguments()
    {
        return array_merge(array_filter([
            $this->command,
            join(' ', $this->options),
            $this->source
        ]), $this->arguments);
    }
}
