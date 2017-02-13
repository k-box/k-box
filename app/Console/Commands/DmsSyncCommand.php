<?php namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\BufferedOutput;
use KlinkDMS\Option;
use KlinkDMS\User;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Traits\Searchable;

class DmsSyncCommand extends Command {
	
	use Searchable;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dms:sync';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Performs a synchronization of the documents from the DMS to the Core. In other words if a document is not found on the core will be added, to force a global reindex use dms:reindex.';

	private $service = null;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsDocuments\DocumentsService $adapterService)
	{
		parent::__construct();
		$this->service = $adapterService;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		
		$debug = $this->getOutput()->getVerbosity() > 1;

		$this->line("Searching for out of sync documents...");
		
		$local_private_id_set = DocumentDescriptor::local()->private()->get()->pluck('local_document_id')->all();
		$local_private_hash_set = DocumentDescriptor::local()->private()->get()->pluck('hash')->all();
		$local_public_id_set = DocumentDescriptor::local()->public()->get()->pluck('local_document_id')->all();
		
		$count_local_private_id_set = count($local_private_id_set);
		$count_local_public_id_set = count($local_public_id_set);
		
		$total_private_on_core = $this->service->getDocumentsCount('private');
		
		if($debug){
			$this->line($count_local_private_id_set . ' local private documents');
		}
		
		$facets_private = \KlinkFacetsBuilder::create()
			// ->institution(\Config::get('dms.institutionID'))
			->localDocumentId(implode(',', $local_private_id_set))->build();
		
		$request = $this->searchRequestCreate()->visibility('private')->limit($total_private_on_core);
		
		$test = $this->search($request, null, function($r){
			return $r->getHash();
		});
		
		// $test = $this->search->search( '*', 0,  $count_local_private_id_set, 'private', $facets_private );
			
		// $mapped = [];
		if(!is_null($test) && $test->total() > 0){
			
			$this->line('> '. $test->total() .' on the core');
			
			// $mapped = array_map(function($r){
			// 	return $r->hash;
			// }, $test->getResults());
		}
		else {
			//no results or error
			if($debug){
				$this->line('> No data from the core');
			}
		}
		
		$hash_diff = array_values(array_diff($local_private_hash_set, $test->items()));
		
		$count_hash_diff = count($hash_diff);
		
		if($count_hash_diff == 0){
		
			$this->line("<comment>All documents are in sync.</comment>");
			
			return 0;	
		}
		
		
		$this->line("Found <info>". $count_hash_diff ."</info> document(s) out of sync...");
		
		if($debug){
			
			$this->line("Hashes of the documents out of sync:");
			
			var_dump($hash_diff);
		}
		
		
		$docs = DocumentDescriptor::whereIn('hash', $hash_diff)->get();
		
		$count_docs = count($docs);
		
		for($i=0;$i<$count_docs;$i++){
		
			$doc = $docs[$i];
		
			$this->write("Reindexing ". $doc->id ."...");
		
			try{
				
				$this->service->reindexDocument($doc);
				
				$this->line('  <info>OK</info>');
					
			}catch(\Exception $ex){
				$this->line('  <error>ERROR '. $ex->getMessage() .'</error>');
			}
		
		}
		
		return 0;

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
		];
	}



	private function log($text){

		$verbosity = $this->getOutput()->getVerbosity();

		if($verbosity > 1){

			$this->line($text);

		}

	}

	private function write($text){

		$verbosity = $this->getOutput()->getVerbosity();

		if($verbosity > 1){

			$this->line($text);

		}
		else {
			$this->getOutput()->write($text);
		}
	}



	private function launch($command, array $arguments = array()){
		$verbosity = $this->getOutput()->getVerbosity();

		if($verbosity > 1){

			return $this->call($command, $arguments);
			
		}

		return $this->callSilent($command, $arguments);
	}

	public function launchAndCapture($command, &$capture, array $arguments = array())
	{
		$instance = $this->getApplication()->find($command);

		$arguments['command'] = $command;

		$out = new BufferedOutput;

		$res = $instance->run(new ArrayInput($arguments), $out);

		$capture = $out->pluck();

		return $res;
	}



	private function doInstallOrUpdate()
	{
		$this->log('searching for previous installation...');

		$result = 0;

		if($this->isInstalled() && $this->isSeeded()){
			$result = $this->doUpdate();
		}
		else {
			$result = $this->doInstall();
		}

		if($result == 0){
			$this->info('  OK');
		}
		else {
			$this->line('  <error>ERROR '. $up_exit_code .'</error>');
		}

		return $result;
	}


	private function isInstalled()
	{

		$exists_options_table = \Schema::hasTable('options');

		$exists_users_table = \Schema::hasTable('users');

		if (!$exists_options_table && !$exists_users_table)
		{
		    return false;
		}

		return true;
	}

	private function isSeeded()
	{
		$c_option = Option::findByKey('c');

		if(is_null($c_option)){
			return false;
		}

		return true;
	}


	private function isAdminUserConfigured()
	{

		$exists = !is_null(User::findByEmail('admin@klink.local'));

		if($exists){
			return true;
		}

		// someone has changed the default administrator user

		$all = User::all();

		foreach ($all as $user) {
			if($user->isDMSAdmin()){
				return true;
			}
		}

		return false;
	}



	private function doInstall()
	{
		$this->write('  <comment>Installing the K-Link DMS...</comment>');

		$db_return = \DB::transaction(function(){

			if(!$this->isInstalled()){
			
				$this->log('    Performing database migration');
				$migrate_result = $this->launch('migrate', array('--force' => true));

				if($migrate_result > 0){
					return $migrate_result;
				}

			}

			if(!$this->isSeeded()){

				$this->log('    Performing database seeding');
				$seed_result = $this->launch('db:seed', array('--force' => true));

				if($seed_result > 0){
					return $seed_result;
				}

				Option::create(array('key' => 'c', 'value' => '' . time()));

			}

			// set info on table that the procedure has been completed succesfully

			

			return 0;

		});

		return $db_return;
	}


	private function doUpdate()
	{
		$this->write('  <comment>Updating the current K-Link DMS installation...</comment>');
		

		// TODO: perform security backup

		$migrate_result = $this->launch('migrate', array('--force' => true));
		
		
		// update the database if needed
		
		if(Capability::syncCapabilities()){
			
			$this->write('  <comment>The user\' capabilities have been upgraded. You might check it in the user\'s management.</comment>');
			
		}

		return $migrate_result;
		
	}



}
