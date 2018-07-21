<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use KBox\User;

class DmsSessionChecker extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dms:sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the user\'s session status.';
    
    private $debug = false;

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
        $since = $this->option('since');
        
        $this->debug = $this->getOutput()->getVerbosity() > 1;
        
        $this->log("Gathering sessions info on <info>".$this->getLaravel()->environment()."</info>...");
        
        $sessions_table_name = app()->config['session.table'];
        
        $sessions_driver_db = app()->config['session.driver'] === 'database';
        
        $table_exists = \Schema::hasTable($sessions_table_name);
        
        if (! $sessions_driver_db || ($sessions_driver_db && ! $table_exists)) {
            $this->line('<error>Sessions must be stored on the database to use this command.</error>');
            return 1;
        }
        
        $this->log('Database session support... <info>OK</info>');
        
        $sessions = DB::table($sessions_table_name)->where('last_activity', '>=', time() - ($since*60))->get()->all();
        
        $this->log('<info>'.count($sessions).'</info> Sessions active in the last '.$since.' minutes.');

        foreach ($sessions as $session) {
            $payload = @unserialize(base64_decode($session->payload));
            
            $login_user = array_first($payload, function ($value, $key) {
                return starts_with($key, 'login_');
            });
            
            try {
                if (! is_null($login_user)) {
                    $this->line(date(\DateTime::ISO8601, $session->last_activity).' '.User::findOrFail($login_user)->name);
                } else {
                    $this->line(date(\DateTime::ISO8601, $session->last_activity).' '.$session->id);
                }
            } catch (\Exception $ex) {
                $this->line(date(\DateTime::ISO8601, $session->last_activity).' '.$session->id);
                
                $this->log(' > User not found: '.$login_user);
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
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
            ['since', 's', InputOption::VALUE_OPTIONAL, 'The number of minutes for considering a section as active.', 120],
        ];
    }
    
    private function log($text)
    {
        if ($this->debug) {
            $this->line($text);
        }
    }
}
