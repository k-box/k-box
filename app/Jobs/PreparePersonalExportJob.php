<?php

namespace KBox\Jobs;

use Log;
use ZipArchive;
use KBox\Group;
use KBox\Starred;
use KBox\Project;
use KBox\Publication;
use KBox\PersonalExport;
use KBox\DocumentDescriptor;
use Illuminate\Bus\Queueable;
use KBox\Documents\Facades\Files;
use KBox\Http\Resources\StarredDump;
use KBox\Http\Resources\ProjectDump;
use KBox\Http\Resources\DocumentDump;
use KBox\Events\PersonalExportCreated;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use KBox\Http\Resources\CollectionDump;
use Illuminate\Queue\InteractsWithQueue;
use KBox\Http\Resources\PublicationDump;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PreparePersonalExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \KBox\PersonalExport;
     */
    public $export = null;

    private $archiveHandle = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PersonalExport $export)
    {
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Generating personal data export {$this->export->name}...");

        $storage = Storage::disk(config('personal-export.disk'));

        $this->generateDataPackage($storage->path($this->export->name));

        $this->export->generated_at = now();
        $this->export->save();

        event(new PersonalExportCreated($this->export));
    }

    private function generateDataPackage($path)
    {
        $this->archiveHandle = new ZipArchive();
        $this->archiveHandle->open($path, ZIPARCHIVE::CREATE);

        $this->addReadme();
        $this->addUser();
        $this->addStars();
        $this->addCollections();
        $this->addPublications();
        $this->addProjects();
        $this->addDocuments();

        $this->archiveHandle->close();
    }

    private function addReadme()
    {
        $this->archiveHandle->addFromString(
            'readme.txt',
            'This archive contain your personal data and the data you, as a user, uploaded to the K-Box. Data shared or uploaded by other users is not part of this export.'
        );
    }
    
    private function addUser()
    {
        $json = $this->export->user
            ->makeHidden('id')
            ->makeHidden('deleted_at')
            ->makeHidden('email_verified_at')
            ->makeHidden('avatar')
            ->toJson();
        
        $this->archiveHandle->addFromString(
            'user.json',
            $json
        );
    }
    
    private function addCollections()
    {
        $collection_json = CollectionDump::collection(
            Group::personalCollections($this->export->user_id)
                ->get()
        )->toJson();

        $this->archiveHandle->addFromString(
            'collections.json',
            $collection_json
        );
    }
    
    private function addDocuments()
    {
        $documents = DocumentDescriptor::ofUser($this->export->user_id)
                ->local()
                ->with('file')
                ->get();
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
    
    private function addStars()
    {
        $collection_json = StarredDump::collection(
            Starred::ofUser($this->export->user_id)
            ->with('document')
            ->get()
        )->toJson();

        $this->archiveHandle->addFromString(
            'stars.json',
            $collection_json
        );
    }
    
    private function addPublications()
    {
        $collection_json = PublicationDump::collection(
            Publication::where('published_by', $this->export->user_id)->published()
            ->with('document')
            ->get()
        )->toJson();

        $this->archiveHandle->addFromString(
            'publications.json',
            $collection_json
        );
    }
    
    private function addProjects()
    {
        $collection_json = ProjectDump::collection(
            Project::managedBy($this->export->user_id)
            ->with('collection')
            ->get()
        )->toJson();

        $this->archiveHandle->addFromString(
            'projects.json',
            $collection_json
        );
    }
}
