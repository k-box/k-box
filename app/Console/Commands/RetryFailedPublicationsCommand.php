<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use KBox\Jobs\PublishDocumentJob;
use KBox\Publication;

class RetryFailedPublicationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publication:retry 
                                {publication? : the publication to retry. Default retry all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry a failed publication on K-Link';

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
        $id = $this->argument('publication') ?? null;
        
        if ($id) {
            // retry single publication if identifier set

            $this->info("Retrying publication [$id]...");
            
            $this->publish(Publication::findOrFail($id));

            return 0;
        }

        $this->info("Retrying failed publications...");

        Publication::whereNotNull('failed_at')->chunk(30, function ($chunk) {
            $chunk->each(function ($p) {
                $this->publish($p);
            });
        });

        return 0;
    }
    
    protected function publish(Publication $p)
    {
        dispatch_now(new PublishDocumentJob($p));
    }
}
