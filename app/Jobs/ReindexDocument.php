<?php namespace KlinkDMS\Jobs;

use KlinkDMS\Commands\Job;

use KlinkDMS\User;
use KlinkDMS\Option;
use KlinkDMS\DocumentDescriptor;
use Klink\DmsDocuments\DocumentsService;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Command for executing a Document Reindexing
 */
class ReindexDocument extends Job implements ShouldQueue {

	use InteractsWithQueue, SerializesModels;

	private $document = null;
	
	private $user = null;

	private $visibility = null;

	/**
	 * Create a new command instance.
	 * 
	 * @param User               $user       The user that have perfomed the action
	 * @param DocumentDescriptor $document   The document
	 * @param string             $visibility The visibility of the documented that needs to be updated
	 */
	public function __construct(User $user, DocumentDescriptor $document, $visibility)
	{
		$this->document = $document;
		$this->user = $user;
		$this->visibility = $visibility;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle(DocumentsService $service)
	{
		\Log::info('Reindex document', ['document' => $this->document, 'visibility' => $this->visibility, 'user' => $this->user]);

		try{

			$service->reindexDocument($this->document, $this->visibility, false);

			return true;

		}catch(\Exception $ex){

			\Log::error('Exception during reindex document', ['document' => $this->document, 'visibility' => $this->visibility, 'user' => $this->user, 'error' => $ex]);

			return false;
		}
	}

}
