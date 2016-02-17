<?php namespace KlinkDMS\Jobs;

use KlinkDMS\Commands\Job;
use KlinkDMS\User;
use KlinkDMS\Option;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Klink\DmsDocuments\DocumentsService;

/**
 * Command that perform the global reindex of all the documents saved in the DMS
 */
class ReindexAll extends Job implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	private $ids = null;

	/**
	 * Create a new command instance.
	 *
	 * @return void
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

		$errors = array();

		try{

			// $service->reindexAll($docs, true);

			$pending = $total;
			$completed = 0;

			foreach ($docs as $doc) {
				try{
					//if is both private and public reindex on every visibility
					$service->reindexDocument($doc, 'private', true);

					

				}catch(\KlinkException $kex){
					$errors[$doc->id] = $kex;
				}

				$pending =  $pending -1;
				$completed = $completed +1;

				\DB::transaction(function() use($pending, $completed){

					Option::put('dms.reindex.pending', $pending);
	    			Option::put('dms.reindex.completed', $completed);

				});
			}

			//reindexDocument(DocumentDescriptor $descriptor, $visibility = null, $force = false)

			Option::put('dms.reindex.executing', false);

			if(empty($errors)){
				\Log::info('Reindex All procedure completed.');
			}
			else {
				\Log::warning('Reindex All completed with errors', ['errors' => $errors]);
			}

			return true;

		}catch(\Exception $ex){

			Option::put('dms.reindex.executing', false);
			Option::put('dms.reindex.error', trans('errors.reindex_all'));

			\Log::error('Exception during reindex all', ['error' => $ex]);


			return false;
		}
	}

}
