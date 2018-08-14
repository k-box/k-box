<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use KBox\User;
use KBox\DocumentDescriptor;
use Klink\DmsAdapter\KlinkVisibilityType;

class DmsReindexCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dms:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform the reindexing of indexed document descriptors.';

    private $service = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(\KBox\Documents\Services\DocumentsService $adapterService)
    {
        parent::__construct();
        $this->service = $adapterService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // Options

        $only_public = $this->option('only-public');
        
        $only_private = $this->option('only-private');
        
        $force = $this->option('force');
        
        $interpret_as_klink_id = $this->option('klink-id');

        $skip = $this->option('skip');

        $take = $this->option('take');

        $users = $this->option('users');

        if (is_string($take)) {
            $take = intval($take);
        }

        if (is_string($skip)) {
            $skip = intval($skip);
        }

        // Arguments

        $documents = $this->argument('documents');

        // Processing

        if ($interpret_as_klink_id && (is_null($documents) || empty($documents))) {
            throw new \InvalidArgumentException('Option --klink-id can only be used if argument list is not empty.');
        }

        if (! empty($documents) && ! empty($users)) {
            throw new \InvalidArgumentException('Documents cannot be specified in conjunction with option --user.');
        }

        if (! is_null($skip) && $skip < 0) {
            throw new \InvalidArgumentException('Skip must be a positive integer or zero.');
        }

        if (! is_null($take) && ($take <= 0 || ! is_integer($take))) {
            throw new \InvalidArgumentException('Take must be a positive integer. Minimum value 1');
        }

        if (! is_array($documents)) {
            $documents = [ $documents ];
        }

        $query = DocumentDescriptor::local();

        $docs = $only_public ? $query->public() : $query->private();

        if (! is_null($documents) && ! empty($documents)) {
            // get the documents reported by ID or Local_document_id

            $query->whereIn($interpret_as_klink_id ? 'local_document_id' : 'id', $documents);
        }

        if (! empty($users)) {
            if (! is_array($users)) {
                $users = [ $users ];
            }

            $query->whereIn('owner_id', $users);
        }

        if (! is_null($take)) {
            $query->take($take);
        }

        if (! is_null($skip)) {
            $query->skip($skip);
        }

        $docs = $query->get();

        $count_docs = $docs->count();

        $this->line("Reindexing <info>".$count_docs." documents</info>...");

        if (! is_null($take) || ! is_null($skip)) {
            $this->line(sprintf('Take %1$s, Skip %2$s', ! is_null($take) ? $take : 'none', ! is_null($skip) ? $skip : 'none'));
        }

        $last_modified_on = null;

        $reindexed_documents_count = 0;

        $bar = $this->output->createProgressBar($count_docs);
        
        for ($i = 0; $i < $count_docs; $i++) {
            $doc = $docs[$i];
        
            // Save the original updated_at time, so we could go back in time to not mess with the user recent list
            $last_modified_on = $doc->updated_at;

            try {
                if ($doc->isPublic() && $only_public) {
                    $this->service->reindexDocument(
                        $doc,
                        KlinkVisibilityType::KLINK_PUBLIC,
                        $force
                    );
                } else {
                    $this->service->reindexDocument(
                        $doc,
                        KlinkVisibilityType::KLINK_PRIVATE,
                        $force
                    );

                    if ($doc->isPublic() && ! $only_private) {
                        $this->service->reindexDocument(
                            $doc,
                            KlinkVisibilityType::KLINK_PUBLIC,
                            $force
                        );
                    }
                }

                // $this->line('  <info>OK</info>');
        
                $reindexed_documents_count++;
            } catch (\Exception $ex) {
                $this->line(sprintf(
                    '<error>Document %1$s (hash: %2$s user: %3$s) raised error: %4$s</error>',
                    $doc->id,
                    $doc->local_document_id,
                    $doc->user_id,
                    $ex->getMessage()
                ));
            }

            // update the local model. This is important because here we have a cached model that
            // might not contain updates made by the reindex process. If we (later) save the non-updated one
            // the persisted model will not inherit the changes
            $doc = $doc->fresh();

            // Move back the updated_at time to the original one, so we are not messing with the user recent list
            $doc->updated_at = $last_modified_on;

            $doc->timestamps = false; //temporarly disable the automatic upgrade of the updated_at field

            $doc->save();

            $bar->advance();
        }

        $bar->finish();
        $this->line("");
        $this->line("<comment>".$reindexed_documents_count." documents reindexed</comment>.");
        
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
            ['documents', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'The list of documents to reindex given in the form of IDs'],
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
            ['only-public', null, InputOption::VALUE_NONE, 'Consider only the documents that have been published on the network and update only the public version.', null],
            ['only-private', null, InputOption::VALUE_NONE, 'Consider only the private documents and do not reindex the public version.', null],
            ['force', 'f', InputOption::VALUE_NONE, 'Force the rebuild of the document and thumbnail URL.', null],

            ['klink-id', null, InputOption::VALUE_NONE, 'Interpret the documents argument as local document id', null],
            
            ['skip', null, InputOption::VALUE_REQUIRED, 'Enable to skip some documents from the batch operation. Zero based value. Normally used in conjunction with limit', null],
            ['take', null, InputOption::VALUE_REQUIRED, 'The maximum number of documents per batch. If no limit is specified all documents will be reindex in a single batch', null],
            
            ['users', 'u', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter for documents of a specific user. The ID of the User is here expected', null],
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
}
