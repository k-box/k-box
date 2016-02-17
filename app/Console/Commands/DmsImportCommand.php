<?php namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use KlinkDMS\Import;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesCommands;
use KlinkDMS\Commands\ImportCommand;


class DmsImportCommand extends Command {
	
	use DispatchesCommands;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dms:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Performs import from disk operations.';

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
		
		$folder = realpath($this->argument('folder'));
		$is_local = $this->option('local');
		$use_roots = false; //$this->option('use-roots');
		$is_institutional = $this->option('institutional');
		$skip = $this->option('skip');
		$exclude = $this->option('exclude');
		
		if(!$folder){
			$this->error('The specified folder argument "'.$this->argument('folder').'" is not a valid folder');
		}
		
		$this->info('DMS Import command. Please login before proceeding');
		// $username = $this->ask('Username?');
		// $password = $this->secret('Password?');
		
		// Log in test
		
		$user = User::findOrFail(1);

		
		// local first and only path must be the storage folder
		
		// dd(compact('is_local', 'folders', 'paths'));


		\Log::info('Import artisan command', array('folder' => $folder, 'user' => $user));

		$this->line('Gathering folder structure for ' . $folder);
	
		$subdirs = array_merge([$folder],  $this->directories($folder, $skip));

		// var_dump($subdirs);

		Model::unguard();
		
		$parent_group = null;
		if($use_roots){
			$parent_group = $this->service->createGroupsFromFolderPath($user, basename($folder), true, !$is_institutional);
		}
		
		$hash = md5($folder);
		
		foreach ($subdirs as $directory) {
			
			// $filename = str_replace('\\', '/', substr($directory, $root_folder_name_pos));
			
			$hash = md5($directory);
			
			if(!File::existsByHash($hash)){

				$file = File::create(array(
					'name' => basename($directory), // the directory name
					'hash' => $hash, // temp for the directory
					'mime_type' => '', 
					'size' => 0, // directory size is unknown and will not be calculated
					'revision_of' => null,
					'thumbnail_path' => null,
					'path' => $is_local ? $directory : $this->service->constructLocalPathForFolderImport(basename($directory)),
					'user_id'  => $user->id,
					'original_uri'  => $directory,
					'is_folder'  => true
				));
				
				$import = Import::create(array(
					'bytes_expected' => 0,
					'bytes_received' => 0,
					'is_remote' => false,
					'file_id' => $file->id,
					'status' => Import::STATUS_QUEUED,
					'user_id' => $user->id,
					'parent_id' => null,
					'status_message' => Import::MESSAGE_QUEUED				
				));
				
				// only folders will be enqueued, the files in that folders will be grabbed during the async import
	
				// create the corresponding group
	
				$group = $this->service->createGroupsFromFolderPath($user, str_replace(realpath(\Config::get('dms.upload_folder')).DIRECTORY_SEPARATOR, '', $file->path), true, !$is_institutional, $parent_group);
				
				if($debug){
					$this->line('Enqueuing ' . $file->id .':' . $file->name . ' as import ' . $import->id . ' in group ' . $group->id . ':' . $group->name);
				}
				
				\Log::info('Import Enqueued', array('file' => $file, 'import' => $import, 'original_root_folder' => $folder, 'group' => $group));
				
				// \Queue::push('ImportCommand@init', array('user' => $user,'import' => $import, 'copy' => !$is_local, 'group' => $group->id, 'skip' => $skip, 'exclude' => $exclude));
				
				$this->dispatch(new ImportCommand($user, $import, $group, !$is_local, $skip));
			
			}
			else {
				
				$this->comment('Skipping folder "' . basename($directory) .'" because already exists ('.$hash.' = ' . $directory . ')');
				
				\Log::warning('Folder already exists by hash', array('hash' => $hash, 'folder' => $directory));
			}
			
            

        }


	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['folder', InputArgument::REQUIRED, 'The folder you want to import documents from.'],
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
			['local', null, InputOption::VALUE_NONE, 'Consider the folder as the storage path and do not copy files, but only performs indexing and collection creations.', null],
			['institutional', null, InputOption::VALUE_NONE, 'Create institutional collections from folders', null],
			// ['use-roots', null, InputOption::VALUE_NONE, 'Use the specified folder argument as the names for the collections', null],
			['skip', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Skip the folders that match the specified pattern', null],
			['exclude', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude files that match the specified pattern', null],
		];
	}
	
	
	
	
	
	
	/**
	 * Traverse a directory to get all sub-directories
	 */
	function directories($directory, $skip = null)
	{
		$directories = array();

		foreach (Finder::create()->in($directory)->directories()->exclude($skip) as $dir)
		{
			$directories[] = $dir->getPathname();
		}

		return $directories;
	}
	
	function files($directory, $exclude = null)
	{
		$directories = array();

		foreach (Finder::create()->in($directory)->files()->exclude($exclude) as $dir)
		{
			$directories[] = $dir->getPathname();
		}

		return $directories;
	}
	
	


}
