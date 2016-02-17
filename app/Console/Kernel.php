<?php namespace KlinkDMS\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'KlinkDMS\Console\Commands\InspireCommand',
		'KlinkDMS\Console\Commands\DmsModelCreation',
		'KlinkDMS\Console\Commands\DmsCreateAdminUserCommand',
		'KlinkDMS\Console\Commands\DmsTestConfiguration',
		'KlinkDMS\Console\Commands\DmsUpdateCommand',
		'KlinkDMS\Console\Commands\DmsQueueListen',
		'KlinkDMS\Console\Commands\DmsReindexCommand',
		'KlinkDMS\Console\Commands\DmsSyncCommand',
		'KlinkDMS\Console\Commands\DmsSessionChecker',
		'KlinkDMS\Console\Commands\DmsViewClearCommand',
		'KlinkDMS\Console\Commands\DmsImportCommand',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		// $schedule->command('inspire')
		// 		 ->hourly();
	}

}
