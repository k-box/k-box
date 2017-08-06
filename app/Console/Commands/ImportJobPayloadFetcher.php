<?php

namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use KlinkDMS\Import;
use Exception;

class ImportJobPayloadFetcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'import:fetch-payload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the failed job table to retrieve the original payload of the Import job enqueued. This is necessary for use the retry option on the Import Job.';

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
        $id = $this->argument('import');
        $replace = $this->option('replace');
        
        $import = Import::findOrFail($id);
        
        $this->comment("Searching job payload for import ID [$id]...");
        
        $failed_jobs = app('queue.failer')->all();
        
        $mapped = array_map(function ($j) {
            $import_id = $this->get_import_from($j->payload);
            
            return [
                'id' => $j->id,
                'failed_at' => $j->failed_at,
                'payload' =>  $j->payload,
                'import' => $import_id];
        }, $failed_jobs);
        
        
        $filtered = array_values(array_filter($mapped, function ($j) use ($import) {
            if (! is_null($j['import'])) {
                return $j['import'] === $import->id;
            }
            return false;
        }));
        
        $payload = null;
        
        if (count($filtered) == 1) {
            $payload = $filtered[0];
        } elseif (count($filtered) > 1) {
            $this->comment('Found '.count($filtered).' possible payloads');
            
            $this->table(['id', 'failed_at'], array_map(function ($j) {
                return [
                        'id' => $j['id'],
                        'failed_at' => $j['failed_at']];
            }, $filtered));
            
            $selected_id = $this->choice('Select the Job ID?', array_pluck($filtered, 'id'), false);
            
            
            $element = array_first($filtered, function ($value, $k) use ($selected_id) {
                return $value['id'] == $selected_id;
            });
            
            if (empty($element)) {
                throw new Exception('Empty job');
            }
            
            $payload = $element['payload'];
        } else {
            throw new Exception("No payload found for import [$id].");
        }
        
        if (! empty($import->job_payload) && ! $replace) {
            throw new Exception('Import already has a payload. Option "--replace" not used');
        }
        
        if (empty($payload)) {
            throw new Exception('Empty payload');
        }
        
        $import->job_payload = $payload;
        
        $import->save();
        
        return 0;
    }
    
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['import', InputArgument::REQUIRED, 'The ID of the import without a job_payload.'],
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
            ['replace', null, InputOption::VALUE_NONE, 'Replace the already existing job_payload of the import'],
        ];
    }
    
    
    
    protected function get_import_from($payload)
    {
        $decoded = json_decode($payload, true);
         
        $command = array_get($decoded, 'data.command');
         
        preg_match('/Import.*id.*:([\d]+);}/', $command, $matches);
         
        if (count($matches) == 2) {
            return (int)$matches[1];
        }
                  
        return null;
    }
}
