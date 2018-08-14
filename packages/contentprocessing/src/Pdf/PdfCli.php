<?php

namespace KBox\Documents\Pdf;

use RuntimeException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
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

        // $process = $builder->getProcess();
        $storage = Storage::disk('local');

        if (! $storage->exists('extraction-cache/')) {
            $storage->makeDirectory('extraction-cache/');
        }

        // create temporary file
        $extract_in = $storage->path('extraction-cache/'.md5(basename($file)).'.txt');

        $executable = realpath($driver);
        
        $this->process = $process = new Process(
            sprintf('"%1$s" -enc UTF-8 "%2$s" "%3$s"', $executable, $file, $extract_in),
            realpath(base_path(self::CLI_FOLDER))
        );
        
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        $plain_text = null;

        try {
            $process->mustRun();

            $content = file_get_contents($extract_in);
        
            $plain_text = mb_convert_encoding(
                $content,
                'UTF-8',
                            mb_detect_encoding($content, 'UTF-8, ASCII, ISO-8859-1, ISO-8859-2, ISO-8859-3, ISO-8859-4, ISO-8859-5, ISO-8859-6, ISO-8859-7, ISO-8859-8, ISO-8859-9, ISO-8859-10, ISO-8859-13, ISO-8859-14, ISO-8859-15, ISO-8859-16, Windows-1251, Windows-1252, Windows-1254', true)
            );
    
            $storage->delete($extract_in);
            
            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        } catch (ProcessFailedException $ex) {
            $storage->delete($extract_in);
            throw $ex;
        }

        return trim($plain_text, " \t\n\r\0\x0B\x0C");
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
