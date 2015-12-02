<?php namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Filesystem\Filesystem;

class DmsViewClearCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dms:viewclear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear all compiled view files.';
	
	/**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		parent::__construct();
		
		$this->files = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$views = $this->files->glob($this->laravel['config']['view.compiled'].'/*');
        foreach ($views as $view) {
            $this->files->delete($view);
        }
        $this->info('Compiled views cleared!');
	}


}
