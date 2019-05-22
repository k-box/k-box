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
        \KBox\Console\Commands\KboxKeyGenerateCommand::class,
        \KBox\Console\Commands\DmsModelCreation::class,
        \KBox\Console\Commands\DmsCreateAdminUserCommand::class,
        \KBox\Console\Commands\DmsTestConfiguration::class,
        \KBox\Console\Commands\DmsUpdateCommand::class,
        \KBox\Console\Commands\DmsQueueListen::class,
        \KBox\Console\Commands\DmsReindexCommand::class,
        \KBox\Console\Commands\DmsSyncCommand::class,
        \KBox\Console\Commands\DmsSessionChecker::class,
        \KBox\Console\Commands\DmsUserImportCommand::class,
        \KBox\Console\Commands\Collections\DmsCollectionsCommand::class,
        \KBox\Console\Commands\Collections\DmsCollectionsCleanDuplicatesDocumentsCommand::class,
        \KBox\Console\Commands\DmsLanguagePublishCommand::class,
        \KBox\Console\Commands\ThumbnailGenerationCommand::class,
        \KBox\Console\Commands\DocumentsCheckInstitutionCommand::class,
        \KBox\Console\Commands\DocumentsCheckDescriptorCommand::class,
        \KBox\Console\Commands\FlagsCommand::class,
        \KBox\Console\Commands\Language\LanguageCheckCommand::class,
        \KBox\Console\Commands\OrphanFilesCommand::class,
        \KBox\Console\Commands\DocumentUpdatePropertiesCommand::class,
        \KBox\Console\Commands\ClearCancelledDocumentUploadsCommand::class,
        \KBox\Console\Commands\VideoElaborateCommand::class,
        \KBox\Console\Commands\StatisticsCommand::class,
        \KBox\Console\Commands\PrivacyLoadCommand::class,
        \KBox\Console\Commands\TermsLoadCommand::class,
        \KBox\Console\Commands\ActivateReadonlyModeCommand::class,
        \KBox\Console\Commands\DeactivateReadonlyModeCommand::class,
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
