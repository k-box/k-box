<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use KBox\Option;
use KBox\User;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\File;
use Ramsey\Uuid\Uuid;
use KBox\UserOption;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use KBox\Group;
use KBox\Project;
use Illuminate\Support\Str;

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
    public function handle()
    {
        $this->line("Configuring K-Box [<info>".$this->getLaravel()->environment()."</info>]...");

        $user_info = null;

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
            $this->line('  <error>ERROR '.$result.'</error>');
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

        $db_return = DB::transaction(function () {
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
            Option::create(['key' => 'branch', 'value' => config('dms.edition')]);

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
        $this->write('  <comment>Generating UUIDs for existing Files...</comment>');
        $count_generated = $this->generateFilesUuid(100);
        if ($count_generated > 0) {
            $this->write("  - <comment>Generated {$count_generated} file UUIDs.</comment>");
        }
        $this->write('  <comment>Generating UUIDs for existing Document Descriptors...</comment>');
        $count_generated = $this->generateDocumentsUuid(100);
        if ($count_generated > 0) {
            $this->write("  - <comment>Generated {$count_generated} UUIDs.</comment>");
        }
        
        $this->write('  <comment>Generating UUIDs for existing Users...</comment>');
        $count_generated = $this->generateUsersUuid(100);
        if ($count_generated > 0) {
            $this->write("  - <comment>Generated {$count_generated} UUIDs.</comment>");
        }
        
        $this->write('  <comment>Generating UUIDs for existing Groups...</comment>');
        $count_generated = $this->generateGroupsUuid(100);
        if ($count_generated > 0) {
            $this->write("  - <comment>Generated {$count_generated} UUIDs.</comment>");
        }
        
        $this->write('  <comment>Generating UUIDs for existing Projects...</comment>');
        $count_generated = $this->generateProjectsUuid(100);
        if ($count_generated > 0) {
            $this->write("  - <comment>Generated {$count_generated} UUIDs.</comment>");
        }
        
        $this->write('  <comment>Filling upload_completed_at File attribute for existing files...</comment>');
        $count_generated = $this->fillFileUploadCompletedAtForExistingFiles();
        if ($count_generated > 0) {
            $this->write("  - <comment>Updated {$count_generated} Files.</comment>");
        }
        
        $this->write('  <comment>Organizing video files on the filesystem...</comment>');
        $count_moved = $this->moveVideoFilesToUuidFolder();
        if ($count_moved > 0) {
            $this->write("  - <comment>Moved {$count_moved} files.</comment>");
        }
        
        $this->write('  <comment>Migrating publications to new format...</comment>');
        $count_migrated = $this->updatePublications();
        if ($count_migrated > 0) {
            $this->write("  - <comment>Migrated {$count_migrated} publications.</comment>");
        }
        
        $this->write('  <comment>Clearing terms_accepted User option...</comment>');
        $this->clearTermsAcceptedUserOption();

        $this->write('  <comment>Upgrading project managers capabilities...</comment>');
        $this->ensureCreateProjectsCapabilityIsSet();

        $this->write('  <comment>Remove deleted capabilities...</comment>');
        $this->removeDeletedCapabilities();

        // check the installed db branch
        
        $b_option = Option::findByKey('branch');

        if (is_null($b_option)) {
            // if empty => write db version
            
            Option::create(['key' => 'branch', 'value' => config('dms.edition')]);
        } else {
            // if project => do project to only upgrade
            // if standard => do standard to only upgrade
        }

        return $migrate_result;
    }

    private function generateDocumentsUuid($chunkSize = 10)
    {
        $count = DocumentDescriptor::local()->count();

        if ($count === 0) {
            return 0;
        }

        $counter = 0;

        $zero_uuid = Uuid::fromString("00000000-0000-0000-0000-000000000000");

        DocumentDescriptor::local()->chunk($chunkSize, function ($documents) use (&$counter, $zero_uuid) {
            foreach ($documents as $doc) {
                $is_current_valid = $doc->uuid && Uuid::isValid($doc->uuid);
                $current = $is_current_valid ? Uuid::fromString($doc->uuid) : false;
                
                if (($is_current_valid && $current->equals($zero_uuid)) ||
                     ! $is_current_valid ||
                    ($is_current_valid && $current && $current->getVersion() !== 4)) {
                    $doc->uuid = Uuid::{$doc->resolveUuidVersion()}();
                    //temporarly disable the automatic upgrade of the updated_at field
                    $doc->timestamps = false;
                    $doc->save();
                    $counter++;
                }
            }
        });
        
        return $counter;
    }

    private function generateUsersUuid($chunkSize = 10)
    {
        $count = User::count();

        if ($count === 0) {
            return 0;
        }

        $counter = 0;

        $zero_uuid = Uuid::fromString("00000000-0000-0000-0000-000000000000");

        User::chunk($chunkSize, function ($users) use (&$counter, $zero_uuid) {
            foreach ($users as $user) {
                $is_current_valid = $user->uuid && Uuid::isValid($user->uuid);
                $current = $is_current_valid ? Uuid::fromString($user->uuid) : false;
                
                if (($is_current_valid && $current->equals($zero_uuid)) ||
                     ! $is_current_valid ||
                    ($is_current_valid && $current && $current->getVersion() !== 4)) {
                    $user->uuid = Uuid::{$user->resolveUuidVersion()}();
                    //temporarly disable the automatic upgrade of the updated_at field
                    $user->timestamps = false;
                    $user->save();
                    $counter++;
                }
            }
        });
        
        return $counter;
    }

    private function generateGroupsUuid($chunkSize = 10)
    {
        $count = Group::count();

        if ($count === 0) {
            return 0;
        }

        $counter = 0;

        $zero_uuid = Uuid::fromString("00000000-0000-0000-0000-000000000000");

        Group::chunk($chunkSize, function ($groups) use (&$counter, $zero_uuid) {
            foreach ($groups as $group) {
                $is_current_valid = $group->uuid && Uuid::isValid($group->uuid);
                $current = $is_current_valid ? Uuid::fromString($group->uuid) : false;
                
                if (($is_current_valid && $current->equals($zero_uuid)) ||
                     ! $is_current_valid ||
                    ($is_current_valid && $current && $current->getVersion() !== 4)) {
                    $group->uuid = Uuid::{$group->resolveUuidVersion()}();
                    //temporarly disable the automatic upgrade of the updated_at field
                    $group->timestamps = false;
                    $group->save();
                    $counter++;
                }
            }
        });
        
        return $counter;
    }

    private function generateProjectsUuid($chunkSize = 10)
    {
        $count = Project::count();

        if ($count === 0) {
            return 0;
        }

        $counter = 0;

        $zero_uuid = Uuid::fromString("00000000-0000-0000-0000-000000000000");

        Project::chunk($chunkSize, function ($projects) use (&$counter, $zero_uuid) {
            foreach ($projects as $project) {
                $is_current_valid = $project->uuid && Uuid::isValid($project->uuid);
                $current = $is_current_valid ? Uuid::fromString($project->uuid) : false;
                
                if (($is_current_valid && $current->equals($zero_uuid)) ||
                     ! $is_current_valid ||
                    ($is_current_valid && $current && $current->getVersion() !== 4)) {
                    $project->uuid = Uuid::{$project->resolveUuidVersion()}();
                    //temporarly disable the automatic upgrade of the updated_at field
                    $project->timestamps = false;
                    $project->save();
                    $counter++;
                }
            }
        });
        
        return $counter;
    }
    
    private function generateFilesUuid($chunkSize = 10)
    {
        $count = File::count();

        if ($count === 0) {
            return 0;
        }

        $counter = 0;

        $zero_uuid = Uuid::fromString("00000000-0000-0000-0000-000000000000");

        File::chunk($chunkSize, function ($files) use (&$counter, $zero_uuid) {
            foreach ($files as $file) {
                $is_current_valid = $file->uuid && Uuid::isValid($file->uuid);
                $current = $is_current_valid ? Uuid::fromString($file->uuid) : false;
                
                if (($is_current_valid && $current->equals($zero_uuid)) ||
                     ! $is_current_valid ||
                    ($is_current_valid && $current && $current->getVersion() !== 4)) {
                    $file->uuid = Uuid::{$file->resolveUuidVersion()}();
                    //temporarly disable the automatic upgrade of the updated_at field
                    $file->timestamps = false;
                    $file->save();
                    $counter++;
                }
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

    private function updatePublications()
    {
        $public_descriptors = DocumentDescriptor::where('is_public', true)->doesntHave('publications')->get();
        
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

    private function moveVideoFilesToUuidFolder()
    {
        $files = File::where('mime_type', 'video/mp4')->get();

        $counter = 0;
        
        $files->each(function ($file) use (&$counter) {
            if (is_file($file->absolute_path) &&
                ! Str::endsWith(dirname($file->path), "$file->uuid")) {
                $dir = dirname($file->path);
    
                Storage::disk('local')->makeDirectory("$dir/$file->uuid");
    
                $extension = pathinfo($file->path, PATHINFO_EXTENSION);
    
                $move_location = "$dir/$file->uuid/$file->uuid.$extension";
                Storage::disk('local')->move($file->path, $move_location);
    
                $file->path = $move_location;
    
                $file->timestamps = false;
    
                $file->save();
    
                $counter++;
            }
        });

        return $counter;
    }

    private function clearTermsAcceptedUserOption()
    {
        UserOption::where('key', 'terms_accepted')->delete();
    }

    private function removeDeletedCapabilities()
    {
        $capabilies_to_remove = [
            Capability::MANAGE_USERS,
            Capability::MANAGE_LOG,
            Capability::MANAGE_BACKUP,
            Capability::IMPORT_DOCUMENTS,
            Capability::MANAGE_PEOPLE_GROUPS,
            Capability::MANAGE_PERSONAL_PEOPLE_GROUPS,
            Capability::SHARE_WITH_PRIVATE,
        ];

        // Detach capabilities from users
        User::whereHas('capabilities', function ($query) use ($capabilies_to_remove) {
            $query->whereIn('key', array_values($capabilies_to_remove));
        })->with('capabilities')->chunk(100, function ($users) use ($capabilies_to_remove) {
            foreach ($users as $user) {
                foreach ($capabilies_to_remove as $capability) {
                    if ($user->can_capability($capability)) {
                        $user->removeCapability($capability);
                    }
                }
            }
        });

        // Remove capabilities from the database
        Capability::whereIn('key', array_values($capabilies_to_remove))->delete();
    }

    /**
     * Ensures that the create_projects capability is added to
     * pre-existing project managers
     */
    private function ensureCreateProjectsCapabilityIsSet()
    {
        $c_option = Option::findByKey('u_cap_cr_prj');

        if (! is_null($c_option)) {
            return ;
        }

        $capabilities_to_search = collect(Capability::$PROJECT_MANAGER)->reject(function ($value, $key) {
            return $value === Capability::CREATE_PROJECTS;
        })->toArray();

        User::with('capabilities')->chunk(100, function ($users) use ($capabilities_to_search) {
            foreach ($users as $user) {
                if ($user->can_all_capabilities($capabilities_to_search)) {
                    $user->addCapability(Capability::CREATE_PROJECTS);
                }
            }
        });

        Option::create(['key' => 'u_cap_cr_prj', 'value' => ''.config('dms.version')]);
    }
}
