<?php namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use KlinkDMS\Import;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Group;
use KlinkDMS\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use KlinkDMS\Jobs\ImportCommand;

use KlinkDMS\Console\Traits\Login;
use KlinkDMS\Console\Traits\DebugOutput;

use KlinkDMS\Exceptions\ForbiddenException;


class DmsImportCommand extends Command {
	
	use DispatchesJobs, Login, DebugOutput;

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
		$use_roots = $this->option('also-current-folder');
		$is_project = $this->option('create-projects');
		$skip = $this->option('skip');
		$exclude = $this->option('exclude');
		$user_param = $this->option('user');
		$enable_file_conflict_resolution = $this->option('attempt-to-resolve-file-conflict');
		
		if(!$folder){
                        
            throw new \Exception('The specified folder "'.$this->argument('folder').'" is not a valid folder');
            
		}
		
		$this->info('DMS Import command. Please login before proceeding');
		
        $this->askLogin();
		
		$user = is_null($this->user()) ? User::findOrFail( $user_param ) : $this->user();

        if(!$user->isProjectManager()){
            throw new ForbiddenException("The user must be at least a project administrator", 1);            
        }
		
		// local first and only path must be the storage folder
		
		// dd(compact('is_local', 'folders', 'paths'));


		\Log::info('Import artisan command', array('folder' => $folder, 'user' => $user));

		$this->line('Gathering folder structure for ' . $folder);
	
		$subdirs = $this->directories($folder, $skip);

		// var_dump($subdirs);

		Model::unguard();
		
		$parent_group = null;
		if($use_roots){
            $subdirs = array_merge([$folder],  $this->directories($folder, $skip));
			// $parent_group = $this->service->createGroupsFromFolderPath($user, basename($folder), true, !$is_project);
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
	
				$group = $this->service->createGroupsFromFolderPath($user, str_replace(realpath(\Config::get('dms.upload_folder')).DIRECTORY_SEPARATOR, '', $file->path), true, !$is_project, $parent_group);
                
                if(!$use_roots && $is_project){
                    $this->debugLine('Creating project from ' . $group->id . ':' . $group->name);
                    
                    $newProject = Project::create(array(
                        'user_id' => $user->id,
                        'name' => $group->name,
                        'description' => '',
                        'collection_id' => $group->id
					));
                }
				
                $this->line('Enqueuing ' . $file->id .':' . $file->name . ' as import ' . $import->id . ' in group ' . $group->id . ':' . $group->name);
				
				\Log::info('Import Enqueued', array('file' => $file, 'import' => $import, 'original_root_folder' => $folder, 'group' => $group));
				
				// $this->dispatch(new ImportCommand($user, $import, $group, !$is_local, $skip, $this->getOutput()));
                // Foce to handle the import in place instead of using the queue
				$command = with(new ImportCommand($user, $import, $group, !$is_local, $skip, $this->getOutput()));
                
                if($enable_file_conflict_resolution){
                    $command->useFileConflictResolution();
                }
                
                $command->handle($this->service);
			
			}
			else {
				
				$this->comment('Skipping folder "' . basename($directory) .'" because already exists ('.$hash.' = ' . $directory . ')');
				
				\Log::warning('Folder already exists by hash', array('hash' => $hash, 'folder' => $directory));
			}
			
            

        }
        
        $this->info('Import process completed.');

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
			['folder', InputArgument::REQUIRED, 'The folder you want to import.'],
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
			['local', 'l', InputOption::VALUE_NONE, 'Consider the folder as the storage path and do not copy files, but only performs indexing and collection creations.', null],
			['create-projects', 'p', InputOption::VALUE_NONE, 'Create projects from folders without a parent', null],
			['user', 'u', InputOption::VALUE_REQUIRED, 'Specify the user that will be the owner of the created collections and documents', null],
			['also-current-folder', 'c', InputOption::VALUE_NONE, 'Use the specified folder argument as the source for all collections and import files that are stored in it', null],
			['skip', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Skip the folders that match the specified pattern', null],
			['exclude', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude files that match the specified pattern', null],
			['attempt-to-resolve-file-conflict', 'd', InputOption::VALUE_NONE, '', null],
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
