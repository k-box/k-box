<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use KBox\File;

class RefreshFileMimeTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:fix-type {file? : The file (ID) to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the mime type of a file is recognized properly and update it in case changes are required';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fileArgument = $this->argument('file') ?? null;

        if (! is_null($fileArgument)) {
            $file = File::findOrFail($fileArgument);

            $updated = $file->updateMimeType();

            if ($updated) {
                $this->line("Mime type updated for the specified file.");
                $this->line("Execute php artisan documents:check-latest-version {$file->document_id} to ensure changes are propagated.");
            }

            return 0;
        }

        $updatedFiles = 0;

        File::chunk(100, function ($files) use (&$updatedFiles) {
            $files->each(function ($file) use (&$updatedFiles) {
                $updated = $file->updateMimeType();

                if ($updated) {
                    $updatedFiles++;
                }
            });
        });

        $this->line("Mime type updated for {$updatedFiles} file.");

        if ($updatedFiles > 0) {
            $this->line("Execute php artisan documents:check-latest-version to ensure changes are propagated.");
        }

        return 0;
    }
}
