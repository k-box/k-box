<?php

namespace KBox\Console\Commands;

use League\Csv\Writer;
use KBox\DocumentDescriptor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Facades\Files;
use KBox\File;
use KBox\RoutingHelpers;
use ZipArchive;

class ExportPublicDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:published {--only-list : Export only the list of documents}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export public documents to zip file or export a CSV file with the list of public documents';

    private $archiveHandle;

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
        return "publications-$date.$extension";
    }

    protected function getPath($name = null)
    {
        return Storage::disk('app')->path($name ?? $this->getExportName());
    }

    private function addReadme()
    {
        $this->archiveHandle->addFromString(
            'readme.txt',
            'This archive contain the export of the documents published on K-Link by users of the K-Box. The included CSV lists the documents and the available information. The CSV file is UTF-8 encoded.'
        );
    }

    private function generateDataPackage($path)
    {
        $this->archiveHandle = new ZipArchive();
        $this->archiveHandle->open($path, ZipArchive::CREATE);

        try {
            $this->addReadme();
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
        $graph[] = ['id', 'title', 'file', 'publication_date', 'license' ,'projects', 'collections', 'hash', 'url'];

        $documents->each(function ($d) use (&$graph) {
            $graph[] = [
                $d->uuid,
                $d->title,
                $this->filePathForZip($d->file),
                optional($d->publication()->published_at)->toDateTimeString(),
                optional($d->copyright_usage)->name ?? 'Copyright',
                $d->projects()->pluck('name')->join('.'),
                $d->groups()->public()->pluck('name')->join('.'),
                $d->hash,
                RoutingHelpers::download($d),
            ];
        });

        $writer = Writer::createFromString();

        $writer->insertAll($graph);

        $this->archiveHandle->addFromString(
            $this->getExportName('csv'),
            $writer->getContent()
        );

        foreach ($documents as $doc) {
            $this->archiveHandle->addFile(
                $doc->file->absolute_path,
                $this->filePathForZip($doc->file)
                //
            );
        }
    }

    private function filePathForZip(File $file)
    {
        return $file->created_at->format('Y/m').'/'.$file->uuid.'.'.Files::extensionFromType($file->mime_type);
    }

    private function getDocuments()
    {
        return DocumentDescriptor::whereHas('publications', function ($query) {
            return $query->whereNotNull('published_at');
        })->with(['publications', 'file'])->orderBy('id')->get();
    }

    private function generateReport()
    {
        $public = $this->getDocuments();

        $graph = [];
        $graph[] = ['id', 'title', 'file', 'publication_date', 'license' ,'projects', 'collections', 'hash', 'url'];

        $public->each(function ($d) use (&$graph) {
            $graph[] = [
                $d->uuid,
                $d->title,
                $d->file->path,
                optional($d->publication()->published_at)->toDateTimeString(),
                optional($d->copyright_usage)->name ?? 'Copyright',
                $d->projects()->pluck('name')->join('.'),
                $d->groups()->public()->pluck('name')->join('.'),
                $d->hash,
                RoutingHelpers::download($d),
            ];
        });

        return $graph;
    }

    private function generateCsv($path)
    {
        $csv = Writer::createFromPath($path, 'w');

        $csv->insertAll($this->generateReport());
    }
}
