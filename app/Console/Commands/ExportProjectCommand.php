<?php

namespace KBox\Console\Commands;

use League\Csv\Writer;
use KBox\DocumentDescriptor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use KBox\Documents\Facades\Files;
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
        $text = <<<EOL
This archive contains the export of the "{$this->project->name}" project, i.e. the records and collections, as folders and files. 

You will find the following files and folders

- "documents.csv" lists the documents contained in the export with the basic metadata (title, folder, author, language, ...).
The file is formatted according to the Comma Separated Value standard using 8-character UTF encoding.
- "project-abstract.txt" contains the description of the project, if added.
- "{$this->project->name}" the folder containing the project files and subfolders.

### Open documents.csv

The file can be opened with Excel with a double click.
Once opened you will see all the text in the first column.

For better viewing please consider doing the following actions:

1. Select the first column by clicking on the column header
2. From the data menu choose the action "Text to columns"
3. A wizard will open that will ask you some options
4. At the first question choose "delimited" and press "next"
5. The separator (or delimiter) is the comma, so check it and deselect the others. You should see a preview below with two columns, one called "id" and the other "title"
6. Press the "next" button until the only action is to finish
7. Now you should see all the text correctly divided into columns
8. From the data menu press "filter".
9. This will add filters on the first line so that you can quickly sort or find the relevant information

### Columns in documents.csv

- "id": The unique identifier of the document
- "title": The title of the document
- "uploaded_at": When the document was added to the K-Box
- "file": The location of the file inside the zip archive
- "language": The recognized language of the document
- "document_type": The format of the document, e.g. pdf-document, image, ...
- "uploader": The user who uploaded the document
- "authors": The document's author(s), if added
- "license": The license of the document
- "projects": The project that contained the document
- "collections": The collection where the document was added
- "hash": An alphanumeric string that can be used to verify that the content of the document has not been altered
- "url": The url of the document inside the K-Box

The file may contain duplicates in the "id" column, as the same document can be added to multiple collections.
Each document is represented according to the folders that are added.
EOL;
        
        $this->archiveHandle->addFromString(
            'readme.txt',
            $text
        );
    }

    private function addAbstract()
    {
        $this->archiveHandle->addFromString(
            'project-abstract.txt',
            $this->project->description
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

        $graph = $this->generateReport();

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
                    $f.'/'.$this->filePathForZip($doc)
                );
                $this->archiveHandle->addFromString(
                    $f.'/'.$this->filePathForZip($doc, 'json'),
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

    private function filePathForZip(DocumentDescriptor $doc, $extension = null)
    {
        $slug = Str::slug($doc->title);

        if (! is_null($extension)) {
            return $slug.'.'.$extension;
        }
        return $slug.'.'.Files::extensionFromType($doc->file->mime_type);
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
            $this->convertDocumentDescritor($d)->each(function ($conv) use (&$graph) {
                $graph[] = $conv;
            });
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
        return $this->getFolders($d)->map(function ($f) use ($d) {
            return [
                $d->uuid,
                $d->title,
                optional($d->created_at)->toDateTimeString(),
                $f.'/'.$this->filePathForZip($d),
                $d->language,
                $d->document_type,
                $d->owner->name,
                $d->authors ?? '',
                optional($d->copyright_usage)->name ?? 'Copyright',
                $d->projects()->pluck('name')->unique()->join('/'),
                $f,
                $d->hash,
                RoutingHelpers::download($d),
            ];
        });
    }
}
