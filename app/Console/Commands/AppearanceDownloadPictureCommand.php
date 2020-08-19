<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use KBox\Jobs\DownloadAppearancePicture;

class AppearanceDownloadPictureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appearance:downloadpicture {--now} {--p|picture=} {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Caches locally the picture defined in the appearance';

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
        $force = $this->option('force');
        $now = $this->option('now');
        $picture = $this->option('picture') ?? config('appearance.picture');

        if ($now) {
            dispatch_now(new DownloadAppearancePicture($picture, $force));
        } else {
            dispatch(new DownloadAppearancePicture($picture, $force));
        }
        
        $this->line("Picture download dispatched");

        return 0;
    }
}
