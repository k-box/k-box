<?php

namespace KBox\Jobs\Updates;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use KBox\Group;

class SynchronizeCollectionTypes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            Group::whereIsPrivate(true)->update(['type' => Group::TYPE_PERSONAL]);
    
            Group::whereIsPrivate(false)->update(['type' => Group::TYPE_PROJECT]);
        });
    }
}
