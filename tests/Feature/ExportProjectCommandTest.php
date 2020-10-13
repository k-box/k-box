<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use KBox\DocumentDescriptor;
use KBox\Documents\Facades\Files;
use KBox\Group;
use KBox\Project;
use KBox\RoutingHelpers;
use League\Csv\Reader;
use ZipArchive;

class ExportProjectCommandTest extends TestCase
{
    use DatabaseTransactions;

    private function createProject()
    {
        $project = factory(Project::class)->create();

        $project->collection->documents()->save(factory(DocumentDescriptor::class)->create());

        $first_level = tap(factory(Group::class, 2)->create([
            'parent_id' => $project->collection->getKey()
        ]), function ($s) {
            $s->each(function ($g) {
                $g->documents()->save(factory(DocumentDescriptor::class)->create());
            });
        });

        $parent = $first_level->first()->getKey();

        $second_level = tap(factory(Group::class, 2)->create([
            'parent_id' => $parent
        ]), function ($s) {
            $s->each(function ($g) {
                $g->documents()->save(factory(DocumentDescriptor::class)->create());
            });
        });

        return $project;
    }

    public function test_csv_with_document_listing_is_generated()
    {
        Storage::fake('app');

        $project = $this->createProject();

        $date = Carbon::today()->toDateString();

        $exitCode = Artisan::call('export:project', [
            'project' => $project->getKey(),
            '--only-list' => true
        ]);

        $this->assertEquals(0, $exitCode);

        $expectedFile = "export-{$project->id}-{$project->title_slug}-{$date}.csv";
        
        Storage::disk('app')->assertExists($expectedFile);

        $csv = Reader::createFromPath(Storage::disk('app')->path($expectedFile), 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset

        $headers = $csv->getHeader();

        $records = collect($csv->getRecords())->values()->toArray();

        $this->assertEquals([
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
        ], $headers);

        $this->assertEquals($project->documents()->orderBy('id')->get()->map(function ($d) {
            return [
                'id' => $d->uuid,
                'title' => $d->title,
                'uploaded_at' => $d->created_at->toDateTimeString(),
                'file' => $d->file->path,
                'language' => $d->language,
                'document_type' => $d->document_type,
                'uploader' => $d->owner->name,
                'authors' => $d->authors,
                'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                'projects' => $d->projects()->pluck('name')->join('.'),
                'collections' => $d->groups()->public()->pluck('name')->join('.'),
                'hash' => $d->hash,
                'url' => RoutingHelpers::download($d),
            ];
        })->toArray(), $records);
    }

    public function test_project_export_include_files_and_folders()
    {
        Storage::fake('app');

        $project = $this->createProject();

        $documents = $project->documents()->get();

        $files_map = $documents->map(function ($d) {
            $collections = $d->groups->map(function ($g) {
                return $g->ancestors()->get()->pluck('name')->merge($g->name)->join('/');
            })->join('/').'/';

            return [
                $collections.$d->file->uuid.'.'.Files::extensionFromType($d->file->mime_type),
                $collections.$d->file->uuid.'.json',
            ];
        })->flatten();

        $date = Carbon::today()->toDateString();

        $exitCode = Artisan::call('export:project', [
            'project' => $project->getKey()
        ]);

        $this->assertEquals(0, $exitCode);

        $expectedFile = "export-{$project->id}-{$project->title_slug}-{$date}.zip";
        
        Storage::disk('app')->assertExists($expectedFile);
        
        $entries = [];

        $zip = tap(new ZipArchive(), function ($z) use ($expectedFile) {
            $z->open(Storage::disk('app')->path($expectedFile));
        });

        $csv_content = $zip->getFromName("documents.csv");

        $elementsInZipFile = $zip->count();

        for ($i=0; $i < $elementsInZipFile; $i++) {
            $entry = $zip->statIndex($i);
            $entries[] = $entry['name'];
        }

        $zip->close();

        $files = collect([
            'readme.txt',
            "project-abstract.txt",
            "documents.csv",
        ])->merge($files_map);

        $this->assertEquals($files->toArray(), $entries);

        $csv = Reader::createFromString($csv_content);
        $csv->setHeaderOffset(0); //set the CSV header offset

        $headers = $csv->getHeader();

        $records = collect($csv->getRecords())->values()->toArray();

        $this->assertEquals([
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
        ], $headers);

        $this->assertEquals($project->documents()->orderBy('id')->get()->map(function ($d) {
            return [
                'id' => $d->uuid,
                'title' => $d->title,
                'uploaded_at' => $d->created_at->toDateTimeString(),
                'file' => $d->file->path,
                'language' => $d->language,
                'document_type' => $d->document_type,
                'uploader' => $d->owner->name,
                'authors' => $d->authors,
                'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                'projects' => $d->projects()->pluck('name')->join('.'),
                'collections' => $d->groups()->public()->pluck('name')->join('.'),
                'hash' => $d->hash,
                'url' => RoutingHelpers::download($d),
            ];
        })->toArray(), $records);
    }
}
