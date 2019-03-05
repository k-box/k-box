<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use KBox\User;
use KBox\Capability;
use KBox\Project;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use KBox\Console\Traits\Login;
use KBox\Console\Traits\DebugOutput;
use League\Csv\Reader;

use Validator;

/**
 * Create Users by reading a CSV file with a known structure
 */
class DmsUserImportCommand extends Command
{
    use DispatchesJobs, Login, DebugOutput;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'users:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates users in batch by making use of a Comma Separated Value (CSV) file. Users will be created with Partner role if not specified';

    protected $data_row_validation_rules = [
        'username' => 'required|max:255',
        'email' => 'required|email|max:255|unique:users',
        'role' => 'sometimes|in:partner,projectadmin,project-admin,admin',
        'manage_projects' => 'nullable|sometimes|array|exists:projects,name',
        'add_to_projects' => 'nullable|sometimes|array|exists:projects,name',
    ];
    
    private $expected_columns = [
        // column names will be compared lowercase
        'username' => ['user'],
        'email' => ['email', 'mail'],
        'role' => ['role'],
        'manage_projects' => ['manage project', 'manage', 'manage-projects', 'manage-project'],
        'add_to_projects' => ['user of project', 'user of projects', 'add to', 'projects'],
    ];
    
    private $role_mapping = null; // initialized in __construct

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->role_mapping = [
            'partner' => Capability::$PARTNER,
            'projectadmin' => Capability::$PROJECT_MANAGER,
            'project-admin' => Capability::$PROJECT_MANAGER,
            'admin' => Capability::$ADMIN,
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        // Ask for user login with admin account
        
        $this->askLogin();
        
        // load the file
        
        $file = $this->argument('file');
        $delimiter = $this->option('delimiter');
        $value_delimiter = $this->option('value-delimiter');
        
        if (! file_exists($file) && ! is_file($file)) {
            throw new \Exception('The file '.$file.' cannot be found or readed');
        }
        
        $this->debugLine('Reading '.$file.' using "'.$delimiter.'" delimiter');

        $csv = Reader::createFromPath($file);
        $csv->setDelimiter($delimiter);
        
        // check if the file is formatted properly
        
        $this->debugLine('Reading headers from file...');
        
        $headers = $csv->fetchOne();
        
        $headers_count = count($headers);
        
        if (! ($headers_count >= 2 && $headers_count <= 5)) {
            throw new \Exception('Wrong number of columns in the file, expecting 2 to 5 columns, got '.$headers_count);
        }
        
        $index = 0;
        foreach ($this->expected_columns as $key => $value) {
            if (isset($headers[$index])) {
                $current_column = strtolower($headers[$index]);
                
                if (strpos($current_column, '(') !== false) {
                    $current_column = trim(substr($current_column, 0, strpos($current_column, '(')));
                }
                
                if (! in_array($current_column, $value)) {
                    throw new \Exception('Wrong column name, expecting '.implode(' or ', $value).' found '.$current_column.' at index '.$index);
                }
                
                $headers[$index] = $key;
                
                $index++;
            }
        }
        
        $validator = null;
        
        $data = $csv->setOffset(1)->fetchAssoc(array_keys($this->expected_columns), function ($row, $rowOffset) use ($validator, $value_delimiter) {
            $this->debugLine('Reading file line '.$rowOffset);
            
            if (isset($row['role'])) {
                $row['role'] = empty($row['role']) ? 'partner' : strtolower($row['role']);
            }
            
            if (isset($row['manage_projects']) && ! empty($row['manage_projects'])) {
                $row['manage_projects'] = array_filter(explode($value_delimiter, $row['manage_projects']));
            }
            
            if (isset($row['add_to_projects']) && ! empty($row['add_to_projects'])) {
                $row['add_to_projects'] = array_filter(explode($value_delimiter, $row['add_to_projects']));
            }
            
            $validator = Validator::make($row, $this->data_row_validation_rules);
            
            $validator->sometimes('role', 'not_in:partner', function ($input) {
                return ! empty($input->manage_projects);
            });
            
            $is_valid = ! $validator->fails();
            
            $row['line_number'] = $rowOffset;
            $row['is_valid'] = $is_valid;
            
            if (! $is_valid) {
                $this->debugLine('line '.$rowOffset.' Failed the validation and will not be processed');
                
                $row['errors'] = implode(' - ', array_values($validator->errors()->all()));
            }
            
            return $row;
        });
        
        $invalid = [];
        
        $this->info('Creating users...');
        
        foreach ($data as $d) {
            if (! $d['is_valid']) {
                unset($d['is_valid']);
                
                if (is_array($d['manage_projects'])) {
                    $d['manage_projects'] = implode(',', $d['manage_projects']);
                }
                if (is_array($d['add_to_projects'])) {
                    $d['add_to_projects'] = implode(',', $d['add_to_projects']);
                }
                $invalid[] = $d;
            } else {
                $this->createUser($d);
            }
        }
        
        if (! empty($invalid)) {
            $this->info('User Import Completed. ');
            
            $this->comment('Some users cannot be created due to an error in the source data, here is the list.');
            
            $error_table_headers = array_merge($headers, [
                'line',
                'error',
            ]);
            
            $this->table($error_table_headers, $invalid);
        } else {
            $this->info('User Import Completed. ');
        }

        return 0;
    }
    
    private function createUser($parameters)
    {
        $this->line('Creating '.$parameters['email'].'...');
        
        // create the user
        
        $password = User::generatePassword();
        
        $user = User::create([
            'name' => $parameters['username'],
            'email' => $parameters['email'],
            'password' => \Hash::make($password)
        ]);

        $user->addCapabilities($this->role_mapping[$parameters['role']]);
        
        $this->debugLine('User '.$parameters['email'].' created with id='.$user->id.'.');
        
        // Add the user to Projects
        
        if (! empty($parameters['add_to_projects'])) {
            $projects = Project::whereIn('name', $parameters['add_to_projects'])->get();
            
            DB::transaction(function () use ($projects, $user) {
                foreach ($projects as $project) {
                    $project->users()->attach($user->id);
                    $this->debugLine('User '.$user->id.' added to "'.$project->name.'"');
                }

                return true;
            });
        }
        
        // Make the user Project Admin of existing project
        
        if (! empty($parameters['manage_projects'])) {
            $projects_to_manage = Project::whereIn('name', $parameters['manage_projects'])->get();
            
            DB::transaction(function () use ($projects_to_manage, $user) {
                foreach ($projects_to_manage as $project) {
                    $project->users()->attach($project->user_id);
                    
                    $project->user_id = $user->id;
                    
                    $saved = $project->save();
                    
                    $this->debugLine('Project manager for "'.$project->name.'" changed to '.$user->id.'=='.$project->user_id.' ? '.var_export($saved, true));
                }
            });
        }
        
        \Mail::queue(
            'emails.welcome-html',
            ['user' => $user, 'password' => $password],
            function ($message) use ($user) {
                $message->to($user->email, $user->name)->subject(trans('administration.accounts.mail_subject'));
            }
        );
    }
    
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['file', InputArgument::REQUIRED, 'The CSV file with the list of users to create.'],
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
            ['delimiter', null, InputOption::VALUE_REQUIRED, 'The delimiter used in the CSV file for separating columns (e.g. ",", ";",...).', ';'],
            ['value-delimiter', null, InputOption::VALUE_REQUIRED, 'The delimiter used to separate values in a single column (e.g. ",", ":",...).', ','],
        ];
    }
}
