<?php

namespace KBox\Console\Commands;

use KBox\PersonalExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;


class PurgeExpiredPersonalDataExportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-export:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge the expired personal data exports.';

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
     * @return mixed
     */
    public function handle()
    {
        $this->line('Purging expired personal data export packages...');

        PersonalExport::expired()->chunk(100, function ($exports) {
            foreach ($exports as $export) {
                $export->purge();
            }
        });

        $this->line('Expired personal exports purged.');

        return 0;
    }
}
