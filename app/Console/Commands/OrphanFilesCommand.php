<?php

namespace KlinkDMS\Console\Commands;

use Illuminate\Console\Command;
use KlinkDMS\File;

/**
 * Find and remove {@see KlinkDMS\File} that do not have a connection with a {@see KlinkDMS\DocumentDescriptor}
 *
 * The following conditions should be met to consider a file as orphan:
 * - not a revision of another file
 * - not the first version of a File
 * - not in relation with a Document Descriptor
 */
class OrphanFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:orphans {--delete : Mark the orphans as deleted} {--force : Permanently delete the orphans} {--file-paths : Output the paths on disk of the orphan files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and remove files not related to a Document Descriptor';

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
        // gli orfani sono file che non hanno un Document Descriptor legato e che non sono revisioni di un file già esistente

        $is_delete = $this->option('delete');
        $is_force = $this->option('force');
        $is_path_output = $this->option('file-paths');

        if (! $is_path_output) {
            $this->comment('Searching for orphan files...');
        }

        if ($is_force && app()->environment() !== 'testing') {
            if (! $this->confirm('Orphan files will be permanently deleted. This action cannot be undone. Would you like to continue?')) {
                return 1;
            }
        }

        // all files that are not in trash

        $preliminary_orphans = File::doesntHave('document')->doesntHave('revisionOf')->get();
        
        $not_trashed_orphans = $preliminary_orphans->filter(function ($el) {
            $revisions_count = File::where('revision_of', $el->id)->count();

            return $revisions_count == 0;
        });

        
        // next manage orphan files that are already in trash

        $preliminary_trashed_orphans = File::onlyTrashed()->with(['document' => function ($query) {
            $query->withTrashed();
        }])->get();

        $trashed_orphans = $preliminary_trashed_orphans->filter(function ($el) {
            $revisions_count = File::withTrashed()->where('revision_of', $el->id)->count();
            $documents_count = $el->document()->withTrashed()->count();

            return $revisions_count == 0 && $documents_count == 0;
        });

        
        // add all orphans to a single Collection
        $orphans = collect($not_trashed_orphans->all())->merge($trashed_orphans);

        if (! $is_path_output) {
            $this->comment(sprintf('%1$s orphan%2$s found', $orphans->count(), $orphans->count() == 1 ? '': 's'));
        }

        $trashed_string = '';
        $file = null;
        $thumb = null;

        foreach ($orphans as $orphan) {
            $trashed_string = $orphan->trashed() ? '(already trashed)' : '';

            if ($is_delete && ! $orphan->trashed()) {
                $orphan->delete();

                $trashed_string = 'trashed';
            }

            if ($is_force) {
                $file = $orphan->path;
                $thumb = $orphan->thumnbail_path;
                
                $orphan->forceDelete();

                $trashed_string = 'deleted';
            }

            if (! $is_path_output) {
                $this->line(sprintf('%3$s (file_id: %1$s) %2$s', $orphan->id, $trashed_string, $orphan->name));
            } else {
                $this->line(sprintf('%1$s', $orphan->path));
            }
        }

        return 0;
    }
}
