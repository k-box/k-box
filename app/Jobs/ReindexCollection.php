<?php

namespace KBox\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use KBox\Group;

class ReindexCollection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var KBox\Group
     */
    public $collection;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Group $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logs()->info('Reindex Job handling for collection '.$this->collection->getKey(), ['collection' => $this->collection]);

        $this->collection->documents()->chunk(50, function ($documents) {
            foreach ($documents as $doc) {
                dispatch(new ReindexDocument($doc, $doc->visibility));
            }
        });
    }
}
