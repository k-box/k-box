<?php

namespace KBox\Console;

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
        'KBox\Console\Commands\KboxKeyGenerateCommand',
        'KBox\Console\Commands\DmsModelCreation',
        'KBox\Console\Commands\DmsCreateAdminUserCommand',
        'KBox\Console\Commands\DmsTestConfiguration',
        'KBox\Console\Commands\DmsUpdateCommand',
        'KBox\Console\Commands\DmsQueueListen',
        'KBox\Console\Commands\DmsReindexCommand',
        'KBox\Console\Commands\DmsSyncCommand',
        'KBox\Console\Commands\DmsSessionChecker',
        'KBox\Console\Commands\DmsImportCommand',
        'KBox\Console\Commands\DmsUserImportCommand',
        'KBox\Console\Commands\Collections\DmsCollectionsCommand',
        'KBox\Console\Commands\Collections\DmsCollectionsCleanDuplicatesDocumentsCommand',
        'KBox\Console\Commands\DmsLanguagePublishCommand',
        'KBox\Console\Commands\ThumbnailGenerationCommand',
        'KBox\Console\Commands\ImportJobPayloadFetcher',
        'KBox\Console\Commands\DocumentsCheckInstitutionCommand',
        'KBox\Console\Commands\DocumentsCheckDescriptorCommand',
        'KBox\Console\Commands\DmsFlagsCommand',
        'KBox\Console\Commands\Language\LanguageCheckCommand',
        'KBox\Console\Commands\OrphanFilesCommand',
        'KBox\Console\Commands\DocumentUpdatePropertiesCommand',
        'KBox\Console\Commands\ClearCancelledDocumentUploadsCommand',
        'KBox\Console\Commands\VideoElaborateCommand',
        'KBox\Console\Commands\StatisticsCommand',
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
