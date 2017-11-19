<?php

namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use KlinkDMS\Option;
use KlinkDMS\User;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\File;
use KlinkDMS\Institution;
use Ramsey\Uuid\Uuid;

class DmsUpdateCommand extends Command
{

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

    private $retry = 2;

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
        $this->line("Started DMS configuration for <info>".$this->getLaravel()->environment()."</info>...");
        
        $this->write('  Testing K-Link configuration...');
    
        if (! $this->option('no-test')) {
            $config_test = $this->launch('dms:test');
        } else {
            $config_test = -1;
        }

        $user_info = null;

        if ($config_test == 0 || $config_test == -1) {
            $this->line('  <info>'.(($config_test == 0) ? 'OK': 'Skipped').'</info>');

            $this->write('  Enabling maintenance mode...');
            $down_exit_code = $this->launch('down');

            if ($down_exit_code > 0) {
                $this->line('  <error>ERROR '.$down_exit_code.'</error>');
                return 10 + $down_exit_code;
            }

            $this->info('  OK');

            $code = $this->doInstallOrUpdate();

            if ($code > 0) {
                return 20 + $code;
            }

            if (! $this->option('no-optimize')) {
                $this->write('  Optimizing installation...');
                $up_exit_code = $this->launch('optimize');
                
                if ($up_exit_code > 0) {
                    $this->line('  <error>ERROR '.$up_exit_code.'</error>');
                    return 50 + $up_exit_code;
                }
            }
            
            $up_exit_code = $this->launch('route:cache');
            
            if ($up_exit_code > 0) {
                $this->line('  <error>ERROR '.$up_exit_code.'</error>');
                return 50 + $up_exit_code;
            }
            
            $up_exit_code = $this->launch('config:cache');
            
            if ($up_exit_code > 0) {
                $this->line('  <error>ERROR '.$up_exit_code.'</error>');
                return 50 + $up_exit_code;
            }
            
            $this->info('  OK');
            
            
            $this->write('  Clearing cache...');
            $up_exit_code = $this->launch('cache:clear');
            
            if ($up_exit_code > 0) {
                $this->line('  <error>ERROR '.$up_exit_code.'</error>');
                return 60 + $up_exit_code;
            }
            
            $this->write('  Clearing compiled view files...');
            $up_exit_code = $this->launch('view:clear');
            
            if ($up_exit_code > 0) {
                $this->line('  <error>ERROR '.$up_exit_code.'</error>');
                return 70 + $up_exit_code;
            }
            
            $this->info('  OK');
            
            $this->write('  Disabling maintenance mode...');
            $up_exit_code = $this->launch('up');

            if ($up_exit_code > 0) {
                $this->line('  <error>ERROR '.$up_exit_code.'</error>');
                return 40 + $up_exit_code;
            }
            $this->info('  OK');

            if (! is_null($user_info)) {
                $this->line('------');
                $this->line($user_info);
            }

            return 0;
        } else {
            $this->line('  <error>ERROR '.$config_test.'</error>');
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
        return [];
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
            ['no-optimize', null, InputOption::VALUE_NONE, 'Skip the optimization of the installation.', null],
        ];
    }

    private function log($text)
    {
        $verbosity = $this->getOutput()->getVerbosity();

        if ($verbosity > 1) {
            $this->line($text);
        }
    }

    private function write($text)
    {
        $verbosity = $this->getOutput()->getVerbosity();

        if ($verbosity > 1) {
            $this->line($text);
        } else {
            $this->getOutput()->write($text);
        }
    }

    private function launch($command, array $arguments = [])
    {
        $verbosity = $this->getOutput()->getVerbosity();

        if ($verbosity > 1) {
            return $this->call($command, $arguments);
        }

        return $this->callSilent($command, $arguments);
    }

    public function launchAndCapture($command, &$capture, array $arguments = [])
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

        if ($this->isInstalled() && $this->isSeeded()) {
            $result = $this->doUpdate();
        } else {
            $result = $this->doInstall();
        }

        if ($result == 0) {
            $this->info('  OK');
        } else {
            $this->line('  <error>ERROR '.$up_exit_code.'</error>');
        }

        return $result;
    }

    private function isInstalled()
    {
        try {
            $exists_options_table = \Schema::hasTable('options');

            $exists_users_table = \Schema::hasTable('users');

            if (! $exists_options_table && ! $exists_users_table) {
                return false;
            }

            return true;
        } catch (\PDOException $ex) {
            // connection refused
            if ($ex->getCode() === 2002 && $this->retry >= 0) {
                $this->log('Database connection error. Retry ('.$this->retry.')');
                    
                sleep(8 - $this->retry);
                
                $this->retry = $this->retry - 1;
                return $this->isInstalled();
            }
            
            throw $ex;
        }
    }

    private function isSeeded()
    {
        $c_option = Option::findByKey('c');

        if (is_null($c_option)) {
            return false;
        }

        return true;
    }

    private function isAdminUserConfigured()
    {
        $exists = ! is_null(User::findByEmail('admin@klink.local'));

        if ($exists) {
            return true;
        }

        // someone has changed the default administrator user

        $all = User::all();

        foreach ($all as $user) {
            if ($user->isDMSAdmin()) {
                return true;
            }
        }

        return false;
    }

    private function doInstall()
    {
        $this->write('  <comment>Installing the K-Box...</comment>');

        $db_return = \DB::transaction(function () {
            if (! $this->isInstalled()) {
                $this->log('    Performing database migration');
                $migrate_result = $this->launch('migrate', ['--force' => true]);

                if ($migrate_result > 0) {
                    return $migrate_result;
                }
            }

            if (! $this->isSeeded()) {
                $this->log('    Performing database seeding');
                $seed_result = $this->launch('db:seed', ['--force' => true]);

                if ($seed_result > 0) {
                    return $seed_result;
                }

                Option::create(['key' => 'c', 'value' => ''.time()]);
            }

            // set info on table that the procedure has been completed succesfully

            // create the db entry with the edition tag
            Option::create(['key' => 'branch', 'value' => \Config::get('dms.edition')]);

            $this->write('  <comment>Checking default institution...</comment>');
            $count_generated = $this->createDefaultInstitution();
            if ($count_generated > 0) {
                $this->write("  - <comment>Added a default Institution.</comment>");
            }

            return 0;
        });

        return $db_return;
    }

    private function doUpdate()
    {
        $this->write('  <comment>Updating the current K-Box installation...</comment>');

        $migrate_result = $this->launch('migrate', ['--force' => true]);
        
        
        // update the database if needed
        
        if (Capability::syncCapabilities()) {
            $this->write('  <comment>The user\' capabilities have been upgraded. You might check it in the user\'s management.</comment>');
        }
        

        // generate the UUID for the private DocumentDescriptor that don't have it
        $this->write('  <comment>Generating UUIDs for existing Document Descriptors...</comment>');
        $count_generated = $this->generateDocumentsUuid();
        if ($count_generated > 0) {
            $this->write("  - <comment>Generated {$count_generated} UUIDs.</comment>");
        }
        
        
        $this->write('  <comment>Filling upload_completed_at File attribute for existing files...</comment>');
        $count_generated = $this->fillFileUploadCompletedAtForExistingFiles();
        if ($count_generated > 0) {
            $this->write("  - <comment>Updated {$count_generated} Files.</comment>");
        }
        
        $this->write('  <comment>Checking default institution...</comment>');
        $count_generated = $this->createDefaultInstitution();
        if ($count_generated > 0) {
            $this->write("  - <comment>Added a default Institution.</comment>");
        }
        
        $this->write('  <comment>Migrating institution to user profile...</comment>');
        $count_generated = $this->updateUserOrganizationAttributes();
        if ($count_generated > 0) {
            $this->write("  - <comment>Updated {$count_generated} Users.</comment>");
        }
        
        $this->write('  <comment>Migrating publications to new format...</comment>');
        $count_migrated = $this->updatePublications();
        if ($count_migrated > 0) {
            $this->write("  - <comment>Migrated {$count_migrated} publications.</comment>");
        }

        // check the installed db branch
        
        $b_option = Option::findByKey('branch');

        if (is_null($b_option)) {
            
            // if empty => write db version
            
            Option::create(['key' => 'branch', 'value' => \Config::get('dms.edition')]);
        } else {
            
            // if project => do project to only upgrade
            // if standard => do standard to only upgrade
        }

        return $migrate_result;
    }

    private function generateDocumentsUuid()
    {
        $docs = DocumentDescriptor::local()->withNullUuid()->get();

        $count = $docs->count();

        if ($count === 0) {
            return 0;
        }

        $counter = 0;

        $zero_uuid = Uuid::fromString("00000000-0000-0000-0000-000000000000");

        $docs->each(function ($doc) use (&$counter, $zero_uuid) {
            $current = Uuid::fromString($doc->uuid);

            if ($current->equals($zero_uuid) || ! Uuid::isValid($doc->uuid)) {
                $doc->uuid = Uuid::{$doc->resolveUuidVersion()}()->toString();
                //temporarly disable the automatic upgrade of the updated_at field
                $doc->timestamps = false;

                $doc->save();
                $counter++;
            }
        });
        
        return $counter;
    }

    private function fillFileUploadCompletedAtForExistingFiles()
    {
        $docs = File::whereNull('upload_completed_at')->get();

        $count = $docs->count();

        if ($count === 0) {
            return 0;
        }

        $counter = 0;

        $docs->each(function ($doc) use (&$counter) {
            $doc->upload_completed_at = $doc->created_at;
            //temporarly disable the automatic upgrade of the updated_at field
            $doc->timestamps = false;

            $doc->save();
            $counter++;
        });
        
        return $counter;
    }

    private function createDefaultInstitution()
    {
        $existing = Institution::current();

        if (is_null($existing)) {
            Institution::forceCreate([
                'klink_id' => config('dms.institutionID'),
                'name' => 'KLINK',
                'email' => 'info@klink.asia',
                'type' => 'Organization'
            ]);

            return 1;
        }

        return 0;
    }

    private function updateUserOrganizationAttributes()
    {
        $users = User::whereNotNull('institution_id')->with('institution')->get();
        
        $count = $users->count();

        if ($count === 0) {
            return 0;
        }

        $counter = 0;

        $users->each(function ($user) use (&$counter) {
            if ($user->institution) {
                $user->organization_name = $user->institution->name;
                $user->organization_website = $user->institution->url;
                $user->institution_id = null;
                $user->timestamps = false;
                $user->save();
                $counter++;
            }
        });
        
        return $counter;
    }

    private function updatePublications()
    {
        $public_descriptors = DocumentDescriptor::where('is_public', true)->doesntHave('publications')->get();
        // dump($public_descriptors->toArray());
        $counter = 0;
        
        $public_descriptors->each(function ($descriptor) use (&$counter) {
            $descriptor->publications()->create([
                'published_at' => $descriptor->updated_at,
                'pending' => false
            ]);

            $counter++;
        });

        return $counter;
    }
}
