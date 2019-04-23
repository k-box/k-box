<?php

namespace OneOffTech\VideoProcessing\Drivers;

use RuntimeException;
use OneOffTech\VideoProcessing\Exceptions\VideoProcessingFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class VideoCli
{
    const VIDEO_PROCESSING_CLI_EXECUTABLE_NAME = 'video-processing-cli';
    const VIDEO_PROCESSING_CLI_FOLDER = 'bin';

    /**
     * @var \OneOffTech\VideoProcessing\Drivers\VideoCliOptions
     */
    private $options = null;

    /**
     * @var Symfony\Component\Process\Process
     */
    private $process = null;

    /**
     * Create a Video CLI worker instance.
     *
     * @param \OneOffTech\VideoProcessing\Drivers\VideoCliOptions $options
     * @return void
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Get the underlying command line process
     *
     * @return Symfony\Component\Process\Process
     */
    public function process()
    {
        return $this->process;
    }

    public function run()
    {
        $driver = self::getVideoCliExecutable();

        if (is_bool($driver) || realpath($driver) === false) {
            throw new RuntimeException("Invalid Video CLI path [{$driver}].");
        }

        $arguments = array_merge([realpath($driver)], $this->options->toWorkerArguments());
        $cwd = realpath(base_path(self::VIDEO_PROCESSING_CLI_FOLDER));

        $this->process = $process = new Process($arguments, $cwd);
        
        // pass the inputs
        
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        try {
            $process->mustRun();

            if (! $process->isSuccessful()) {
                throw new VideoProcessingFailedException((new ProcessFailedException($process))->getMessage());
            }
        } catch (ProcessFailedException $ex) {
            throw new VideoProcessingFailedException($ex->getMessage());
        }
    }

    /**
     * Return the command output
     *
     * @return string
     */
    public function output()
    {
        return $this->process ? $this->process->getOutput() : null;
    }

    /**
     * Return the command error output
     *
     * @return string
     */
    public function error()
    {
        return $this->process ? $this->process->getErrorOutput() : null;
    }

    /**
     * Return the command exit code
     *
     * @return int
     */
    public function exitCode()
    {
        return $this->process ? $this->process->getExitCode() : -1;
    }

    private static function getVideoCliExecutable()
    {
        $suffixes = [
            '',
            '.exe',
            '-win.exe',
            '-linux',
            '-macos',
        ];

        $dir = realpath(base_path(self::VIDEO_PROCESSING_CLI_FOLDER));

        foreach ($suffixes as $suffix) {
            if (@is_file($file = $dir.DIRECTORY_SEPARATOR.self::VIDEO_PROCESSING_CLI_EXECUTABLE_NAME.$suffix) && ('\\' === DIRECTORY_SEPARATOR || is_executable($file))) {
                return $file;
            }
        }

        throw new RuntimeException("No Video CLI executable found in [{$dir}].");
    }

    public static function isInstalled()
    {
        try {
            static::getVideoCliExecutable();

            return true;
        } catch (RuntimeException $ex) {
            if (starts_with($ex->getMessage(), "No Video CLI executable found")) {
                return false;
            }

            throw $ex;
        }
    }
}
