<?php namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\BufferedOutput;
use KlinkDMS\Option;
use KlinkDMS\User;
use KlinkDMS\Capability;

class DmsUpdateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dms:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Perform the installation/update steps for the K-Link DMS.';

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
	 * @return mixed
	 */
	public function fire()
	{

		$this->line("Started DMS configuration for <info>". $this->getLaravel()->environment() ."</info>...");
		
		$this->write('  Testing K-Link configuration...');
	
		if(!$this->option('no-test')){
			
			$config_test = $this->launch('dms:test');
		
		}
		else {
			$config_test = -1;
		}

		$user_info = null;

		if($config_test == 0 || $config_test == -1){

			$this->line('  <info>'.(($config_test == 0) ? 'OK': 'Skipped').'</info>');

			$this->write('  Enabling maintenance mode...');
			$down_exit_code = $this->launch('down');

			if($down_exit_code > 0){
				$this->line('  <error>ERROR '. $down_exit_code .'</error>');
				return 10 + $down_exit_code;
			}

			$this->info('  OK');

			$code = $this->doInstallOrUpdate();

			if($code > 0){

				return 20 + $code;

			}

			
			$this->write('  Optimizing installation...');
			$up_exit_code = $this->launch('optimize');
			
			if($down_exit_code > 0){
				$this->line('  <error>ERROR '. $up_exit_code .'</error>');
				return 50 + $down_exit_code;
			}
			
			$up_exit_code = $this->launch('route:cache');
			
			if($down_exit_code > 0){
				$this->line('  <error>ERROR '. $up_exit_code .'</error>');
				return 50 + $down_exit_code;
			}
			
			$up_exit_code = $this->launch('config:cache');
			
			if($down_exit_code > 0){
				$this->line('  <error>ERROR '. $up_exit_code .'</error>');
				return 50 + $down_exit_code;
			}
			
			$this->info('  OK');
			
			
			$this->write('  Clearing cache...');
			$up_exit_code = $this->launch('cache:clear');
			
			if($down_exit_code > 0){
				$this->line('  <error>ERROR '. $up_exit_code .'</error>');
				return 60 + $down_exit_code;
			}
			
			$this->write('  Clearing compiled view files...');
			$up_exit_code = $this->launch('dms:viewclear');
			
			if($down_exit_code > 0){
				$this->line('  <error>ERROR '. $up_exit_code .'</error>');
				return 70 + $down_exit_code;
			}
			
			$this->info('  OK');
			
			$this->write('  Disabling maintenance mode...');
			$up_exit_code = $this->launch('up');

			if($down_exit_code > 0){
				$this->line('  <error>ERROR '. $up_exit_code .'</error>');
				return 40 + $down_exit_code;
			}
			$this->info('  OK');


			if(!is_null($user_info)){
				$this->line('------');
				$this->line($user_info);
			}

			return 0;

		}
		else {
			$this->line('  <error>ERROR '. $config_test .'</error>');
		}


		return 2;

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			// ['example', InputArgument::REQUIRED, 'An example argument.'],
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
			['no-test', null, InputOption::VALUE_NONE, 'Disable the connection test to the reference K-Link Core.', null],
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

		$capture = $out->fetch();

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

			// create the db entry with the edition tag
			Option::create(array('key' => 'branch', 'value' => \Config::get('dms.edition')));

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
		
		
		// check the installed db branch
		
		$b_option = Option::findByKey('branch');

		if(is_null($b_option)){
			
			// if empty => write db version
			
			Option::create(array('key' => 'branch', 'value' => \Config::get('dms.edition')));
			
		}
		else {
			
			// if project => do project to only upgrade
			// if standard => do standard to only upgrade
			
		}

		return $migrate_result;
		
	}



}
