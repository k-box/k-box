<?php

namespace KBox\Console\Commands;

use League\Csv\Writer;
use KBox\DocumentDescriptor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use KBox\Documents\Facades\Files;
use KBox\Http\Resources\DocumentDump;
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
        $date = today()->toDateString();
        $onlyReport = $this->option('only-list');

        $this->info("Generating export...");

        if ($onlyReport) {
            $name = "publications-$date.csv";
            $csv_path = Storage::disk('app')->path($name);

            $this->generateCsv($csv_path);

            $this->info("Document list saved [{$name}].");

            return 0;
        }

        $name = "publications-$date.zip";
        $path = Storage::disk('app')->path($name);

        $this->generateDataPackage($path);

        $this->info("Export saved [{$name}].");

        return 0;
    }

    private function addReadme()
    {
        $this->archiveHandle->addFromString(
            'readme.txt',
            'This archive contain the export of the documents published on K-Link by users of the K-Box.'
        );
    }

    private function generateDataPackage($path)
    {
        $this->archiveHandle = new ZipArchive();
        $this->archiveHandle->open($path, ZipArchive::CREATE);

        $this->addReadme();
        $this->addDocuments();

        $this->archiveHandle->close();
    }

    private function addDocuments()
    {
        $documents = $this->getDocuments();

        $collection_json = DocumentDump::collection($documents)->toJson();

        $this->archiveHandle->addFromString(
            'documents.json',
            $collection_json
        );

        foreach ($documents as $doc) {
            $this->archiveHandle->addFile(
                $doc->file->absolute_path,
                $doc->file->uuid.'.'.Files::extensionFromType($doc->file->mime_type)
            );
        }
    }

    private function getDocuments()
    {
        return DocumentDescriptor::whereHas('publications')->with(['publications', 'file'])->orderBy('id')->get();
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
                $d->publication()->published_at->toDateTimeString(),
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
