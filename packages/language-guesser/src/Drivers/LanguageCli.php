<?php

namespace OneOffTech\LanguageGuesser\Drivers;

use Illuminate\Support\Arr;
use RuntimeException;
use OneOffTech\LanguageGuesser\Exceptions\LanguageGuessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LanguageCli
{
    const CLI_EXECUTABLE_NAME = 'language-guesser';
    const CLI_FOLDER = 'bin';

    private $text = null;
    private $all = false;
    private $blacklist = null;
    private $whitelist = null;

    /**
     * @var Symfony\Component\Process\Process
     */
    private $process = null;

    /**
     * Create a Video CLI worker instance.
     *
     * @param \OneOffTech\LanguageGuesser\Drivers\LanguageGuesser $options
     * @return void
     */
    public function __construct($text, $all = false, $blacklist = ['vec'], $whitelist = [])
    {
        $this->text = $text;
        $this->all = $all;
        $this->blacklist = $blacklist;
        $this->whitelist = $whitelist;
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
        $driver = self::getCliExecutable();

        if (is_bool($driver) || realpath($driver) === false) {
            throw new RuntimeException("Invalid Language CLI path [{$driver}].");
        }

        $executable = realpath($driver);

        $options = [];

        // pass the inputs
        if ($this->all) {
            $options[] = '--all';
        }

        if (! empty($this->blacklist)) {
            $options[] = '--blacklist '.join(',', Arr::wrap($this->blacklist));
        }

        if (! empty($this->whitelist)) {
            $options[] = '--whitelist '.join(',', Arr::wrap($this->whitelist));
        }

        $this->process = $process = new Process(
            sprintf('"%1$s" %2$s', $executable, join(' ', $options)),
            realpath(base_path(self::CLI_FOLDER)),
            null,
            $this->text
        );
        
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        try {
            $process->mustRun();

            if (! $process->isSuccessful()) {
                throw new LanguageGuessFailedException((new ProcessFailedException($process))->getMessage());
            }
        } catch (ProcessFailedException $ex) {
            throw new LanguageGuessFailedException($ex->getMessage());
        }

        return $this->output();
    }

    /**
     * Return the command output
     *
     * @return string
     */
    public function output()
    {
        return $this->process ? rtrim($this->process->getOutput()) : null;
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

    private static function getCliExecutable()
    {
        $suffixes = [
            '',
            '.exe',
            '-win.exe',
            '-linux',
            '-macos',
        ];

        $dir = realpath(base_path(self::CLI_FOLDER));

        foreach ($suffixes as $suffix) {
            if (@is_file($file = $dir.DIRECTORY_SEPARATOR.self::CLI_EXECUTABLE_NAME.$suffix) && ('\\' === DIRECTORY_SEPARATOR || is_executable($file))) {
                return $file;
            }
        }

        throw new RuntimeException("No Language CLI executable found in [{$dir}].");
    }

    public static function isInstalled()
    {
        try {
            static::getCliExecutable();

            return true;
        } catch (RuntimeException $ex) {
            if (starts_with($ex->getMessage(), "No Language CLI executable found")) {
                return false;
            }

            throw $ex;
        }
    }
}
