<?php

namespace KlinkDMS\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'KlinkDMS\Console\Commands\DmsModelCreation',
        'KlinkDMS\Console\Commands\DmsCreateAdminUserCommand',
        'KlinkDMS\Console\Commands\DmsTestConfiguration',
        'KlinkDMS\Console\Commands\DmsUpdateCommand',
        'KlinkDMS\Console\Commands\DmsQueueListen',
        'KlinkDMS\Console\Commands\DmsReindexCommand',
        'KlinkDMS\Console\Commands\DmsSyncCommand',
        'KlinkDMS\Console\Commands\DmsSessionChecker',
        'KlinkDMS\Console\Commands\DmsImportCommand',
        'KlinkDMS\Console\Commands\DmsUserImportCommand',
        'KlinkDMS\Console\Commands\Collections\DmsCollectionsCommand',
        'KlinkDMS\Console\Commands\Collections\DmsCollectionsCleanDuplicatesDocumentsCommand',
        'KlinkDMS\Console\Commands\DmsLanguagePublishCommand',
        'KlinkDMS\Console\Commands\ThumbnailGenerationCommand',
        'KlinkDMS\Console\Commands\ImportJobPayloadFetcher',
        'KlinkDMS\Console\Commands\DocumentsCheckInstitutionCommand',
        'KlinkDMS\Console\Commands\DocumentsCheckDescriptorCommand',
        'KlinkDMS\Console\Commands\DmsFlagsCommand',
        'KlinkDMS\Console\Commands\Language\LanguageCheckCommand',
        'KlinkDMS\Console\Commands\OrphanFilesCommand',
        'KlinkDMS\Console\Commands\DocumentUpdatePropertiesCommand',
        'KlinkDMS\Console\Commands\ClearCancelledDocumentUploadsCommand',
        'KlinkDMS\Console\Commands\VideoElaborateCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('documents:clear-cancelled')->withoutOverlapping()->daily();
    }
}
