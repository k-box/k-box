<?php

namespace Content\Pdf;

use RuntimeException;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PdfCli
{
    const EXTRACT_EXECUTABLE = 'pdftotext';
    const INFO_EXECUTABLE = 'pdfinfo';
    const CLI_FOLDER = 'bin';

    /**
     * Create a PDF CLI instance.
     *
     * @return void
     */
    public function __construct()
    {
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

    public function convertToText($file)
    {
        $driver = self::getCliExecutable();

        if (is_bool($driver) || realpath($driver) === false) {
            throw new RuntimeException("Invalid PDF CLI path [{$driver}].");
        }

        $builder = (new ProcessBuilder())
                ->setPrefix(realpath($driver))
                ->setWorkingDirectory(realpath(base_path(self::CLI_FOLDER)));

        $builder->add($file);
        $builder->add('-');

        $process = $builder->getProcess();
        
        // pass the inputs
        
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        try {
            $process->mustRun();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        } catch (ProcessFailedException $ex) {
            throw $ex;
        }

        return trim($process->getOutput(), " \t\n\r\0\x0B\x0C");
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
            if (@is_file($file = $dir.DIRECTORY_SEPARATOR.self::EXTRACT_EXECUTABLE.$suffix) && ('\\' === DIRECTORY_SEPARATOR || is_executable($file))) {
                return $file;
            }
        }

        throw new RuntimeException("No PDF text extract executable found in [{$dir}].");
    }

    public static function isInstalled()
    {
        try {
            static::getCliExecutable();

            return true;
        } catch (RuntimeException $ex) {
            if (starts_with($ex->getMessage(), "No PDF CLI executable found")) {
                return false;
            }

            throw $ex;
        }
    }
}
