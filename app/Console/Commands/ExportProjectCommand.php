<?php

namespace KBox\Console\Commands;

use League\Csv\Writer;
use KBox\DocumentDescriptor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Facades\Files;
use KBox\File;
use KBox\Project;
use KBox\RoutingHelpers;
use ZipArchive;

class ExportProjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:project {project} {--only-list : Export only the list of documents and collections}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export project documents\' and collections\' to zip file or export a CSV file with the list of documents and collections';

    private $archiveHandle;

    /**
     * @var \App\Project
     */
    private $project;

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
     * @return int
     */
    public function handle()
    {
        $onlyReport = $this->option('only-list');
        
        $this->project = Project::findOrFail($this->argument('project'));

        $this->info("Generating export...");

        if ($onlyReport) {
            $path = $this->getPath($this->getExportName('csv'));

            $this->generateCsv($path);

            $this->info("Document list saved [{$path}].");

            return 0;
        }

        $path = $this->getPath($this->getExportName('zip'));

        $this->generateDataPackage($path);

        $this->info("Export saved [{$path}].");

        return 0;
    }

    protected function getExportName($extension = 'zip')
    {
        $date = today()->toDateString();
        return "export-{$this->project->id}-{$this->project->title_slug}-{$date}.$extension";
    }

    protected function getPath($name = null)
    {
        return Storage::disk('app')->path($name ?? $this->getExportName());
    }

    private function addReadme()
    {
        $this->archiveHandle->addFromString(
            'readme.txt',
            'This archive contain the export of the documents and collections contained into "'.$this->project->name.'". The included CSV lists the documents and the available information. The CSV file is UTF-8 encoded.'
        );
    }

    private function addAbstract()
    {
        $this->archiveHandle->addFromString(
            'project-abstract.txt',
            $this->project->abstract
        );
    }

    private function generateDataPackage($path)
    {
        $this->archiveHandle = new ZipArchive();
        $this->archiveHandle->open($path, ZipArchive::CREATE);

        try {
            $this->addReadme();
            $this->addAbstract();
            $this->addDocuments();
        } finally {
            if ($this->archiveHandle) {
                $this->archiveHandle->close();
            }
        }
    }

    private function addDocuments()
    {
        $documents = $this->getDocuments();

        $graph = [];

        $graph[] = $this->getCsvHeaders();

        $documents->each(function ($d) use (&$graph) {
            $graph[] = $this->convertDocumentDescritor($d);
        });

        $writer = Writer::createFromString();

        $writer->insertAll($graph);

        $this->archiveHandle->addFromString(
            'documents.csv',
            $writer->getContent()
        );

        foreach ($documents as $doc) {
            $this->getFolders($doc)->each(function ($f) use ($doc) {
                $this->archiveHandle->addFile(
                    $doc->file->absolute_path,
                    $f.'/'.$this->filePathForZip($doc->file)
                );
                $this->archiveHandle->addFromString(
                    $f.'/'.$this->filePathForZip($doc->file, 'json'),
                    $doc->toJson()
                );
            });
        }
    }

    private function getFolders(DocumentDescriptor $doc)
    {
        return $doc->groups->map(function ($g) {
            return $g->ancestors()->get()->pluck('name')->merge($g->name)->join('/');
        });
    }

    private function filePathForZip(File $file, $extension = null)
    {
        if (! is_null($extension)) {
            return $file->uuid.'.'.$extension;
        }
        return $file->uuid.'.'.Files::extensionFromType($file->mime_type);
    }

    private function getDocuments()
    {
        return DocumentDescriptor::with(['owner', 'file'])->orderBy('id')->get();
    }

    private function generateReport()
    {
        $public = $this->getDocuments();

        $graph = [];
        $graph[] = $this->getCsvHeaders();

        $public->each(function ($d) use (&$graph) {
            $graph[] = $this->convertDocumentDescritor($d);
        });

        return $graph;
    }

    private function generateCsv($path)
    {
        $csv = Writer::createFromPath($path, 'w');

        $csv->insertAll($this->generateReport());
    }

    private function getCsvHeaders()
    {
        return [
            'id',
            'title',
            'uploaded_at',
            'file',
            'language',
            'document_type',
            'uploader',
            'authors',
            'license',
            'projects',
            'collections',
            'hash',
            'url',
        ];
    }

    private function convertDocumentDescritor(DocumentDescriptor $d)
    {
        return [
            $d->uuid,
            $d->title,
            optional($d->created_at)->toDateTimeString(),
            $d->file->path,
            $d->language,
            $d->document_type,
            $d->owner->name,
            $d->authors,
            optional($d->copyright_usage)->name ?? 'Copyright',
            $d->projects()->pluck('name')->join('.'),
            $d->groups()->public()->pluck('name')->join('.'),
            $d->hash,
            RoutingHelpers::download($d),
        ];
    }
}
