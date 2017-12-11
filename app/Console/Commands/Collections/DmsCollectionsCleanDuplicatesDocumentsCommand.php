<?php

namespace KBox\Console\Commands\Collections;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use KBox\User;
use KBox\Group;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DmsCollectionsCleanDuplicatesDocumentsCommand extends Command
{
    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var string
     */
    // protected $name = 'collections:clean-duplicates';
    protected $signature = 'collections:clean-duplicates {collection : The collection identifier} {--yes : with --no-interaction mode answer yes to every question}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the duplicated documents contained in a collection.';

    private $service = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(\Klink\DmsDocuments\DocumentsService $adapterService)
    {
        parent::__construct();
        $this->service = $adapterService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $debug = $this->getOutput()->getVerbosity() > 1;
        
        $grp_arg = $this->argument('collection');
        
        $no_interaction = $this->option('no-interaction');
        $yes = $this->option('yes');
        
        if ($yes && ! $no_interaction) {
            throw new \Exception("--yes option can only be used with --no-interaction", 1);
        }
        
        $group = Group::findOrFail($grp_arg);
        
        // $count_field = \DB::raw('COUNT(*) as copies');
        
        $duplicates_query = $group->documents()
            ->groupBy(['document_id','group_id'])
            ->orderBy(\DB::raw('copies'), 'desc')
            ->having('copies', '>', 1)
            ->get(['document_id',
                'group_id',
                \DB::raw('COUNT(*) as copies'),
            ]);
        
        $duplicates = [];
        $t = null;
        
        foreach ($duplicates_query as $d) {
            $t = $d->pivot->toArray();
            $t['copies'] = $d->copies;
            $t['pivot_ids_remove'] = join(',', $group->documents()->where('document_id', $d->pivot->document_id)->get([
                'document_groups.id as pivot_id',
                'document_id',
                'group_id',
            ])->pluck('pivot.id')->take($d->copies-1)->toArray());
            
            $duplicates[] = $t;
        }
            
        
        if (! empty($duplicates)) {
            $this->table(array_keys($duplicates[0]), $duplicates);
            
            if ($this->confirm('Delete duplicated document->collection association? This action cannot be undone [y|N]') || ($yes && $no_interaction)) {
                foreach ($duplicates as $d) {
                    $this->line('Deleting '.$d['document_id'].'...');
                    $ids = explode(',', $d['pivot_ids_remove']);
                    \DB::table('document_groups')->whereIn('id', $ids)->limit(count($ids))->delete();
                }
                
                $this->info('Completed. ');
            }
        } else {
            $this->info('No duplicates found');
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
        return [
            ['collection', InputArgument::OPTIONAL, 'The collections you want to operate on.'],
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
            // ['transfer', null, InputOption::VALUE_REQUIRED, 'Transfer the collection ownership to the specified user.', null],
            ['user', 'u', InputOption::VALUE_REQUIRED, 'The user to impersonate', 1],
            // ['to-project', 'p', InputOption::VALUE_REQUIRED, 'Move the current collection to a specific Project collection and automatically makes it a project collection', null],
            // ['make-project', null, InputOption::VALUE_NONE, 'Makes the specified collection a project collection', null],
        ];
    }
    
    
    
    
    
    
    /**
     * Traverse a directory to get all sub-directories
     */
    public function directories($directory, $skip = null)
    {
        $directories = [];

        foreach (Finder::create()->in($directory)->directories()->exclude($skip) as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }
    
    public function files($directory, $exclude = null)
    {
        $directories = [];

        foreach (Finder::create()->in($directory)->files()->exclude($exclude) as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }
}
