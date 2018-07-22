<?php

namespace KBox\Jobs;

use KBox\User;
use KBox\Option;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Klink\DmsDocuments\DocumentsService;
use Klink\DmsAdapter\Exceptions\KlinkException;

/**
 * Perform the global reindex of all the documents saved in the K-Box.
 *
 * It runs on the queue.
 */
class ReindexAll extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * The IDs of the document descriptors to be reindexed
     * @var array the array of document descriptor ID to be reindexed
     */
    private $ids = null;

    /**
     * Create a new ReindexAll job instance.
     *
     * @param \KBox\User $user the User that is triggering the reindex
     * @param array|string[] $docIds the array of document descriptor ID to be reindexed
     * @return ReindexAll
     */
    public function __construct(User $user, array $docIds)
    {
        $this->ids = $docIds;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(DocumentsService $service)
    {
        \Log::info('Reindex All procedure started...');

        $docs = DocumentDescriptor::whereIn('id', $this->ids)->get();

        $total = count($docs);

        $errors = [];

        try {
            $pending = $total;
            $completed = 0;

            foreach ($docs as $doc) {
                try {
                    //if is both private and public reindex on every visibility
                    $service->reindexDocument($doc, 'private', true);
                } catch (KlinkException $kex) {
                    $errors[$doc->id] = $kex;
                }

                $pending =  $pending -1;
                $completed = $completed +1;

                DB::transaction(function () use ($pending, $completed) {
                    // update the status of the reindexing
                    Option::put('dms.reindex.pending', $pending);
                    Option::put('dms.reindex.completed', $completed);
                });
            }

            Option::put('dms.reindex.executing', false); // save the execution status

            if (empty($errors)) {
                \Log::info('Reindex All procedure completed.');
            } else {
                \Log::warning('Reindex All completed with errors', ['errors' => $errors]);
            }

            return true;
        } catch (\Exception $ex) {
            Option::put('dms.reindex.executing', false);
            Option::put('dms.reindex.error', trans('errors.reindex_all'));

            \Log::error('Exception during reindex all', ['error' => $ex]);

            return false;
        }
    }
}
