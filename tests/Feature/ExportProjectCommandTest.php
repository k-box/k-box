<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        $doc = factory(DocumentDescriptor::class)->create();

        $project->collection->documents()->save($doc);
        $first_level->first()->documents()->save($doc);
        $second_level->first()->documents()->save($doc);

        return $project;
    }

    private function getFolders(DocumentDescriptor $doc)
    {
        return $doc->groups->map(function ($g) {
            return $g->ancestors()->get()->pluck('name')->merge($g->name)->join('/');
        });
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

        $expectedList = [];
        $project->documents()->orderBy('id')->get()->each(function ($d) use (&$expectedList) {
            $this->getFolders($d)->each(function ($f) use ($d, &$expectedList) {
                $expectedList[] = [
                    'id' => $d->uuid,
                    'title' => $d->title,
                    'uploaded_at' => $d->created_at->toDateTimeString(),
                    'file' => $f.'/'.Str::slug($d->title).'.'.Files::extensionFromType($d->file->mime_type),
                    'language' => $d->language,
                    'document_type' => $d->document_type,
                    'uploader' => $d->owner->name,
                    'authors' => $d->authors,
                    'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                    'projects' => $d->projects()->pluck('name')->unique()->join('/'),
                    'collections' => $f,
                    'hash' => $d->hash,
                    'url' => RoutingHelpers::download($d),
                ];
            });
        });

        $this->assertEquals($expectedList, $records);
    }

    public function test_project_export_include_files_and_folders()
    {
        Storage::fake('app');

        $project = $this->createProject();

        $documents = $project->documents()->orderBy('id')->get();

        $files_map = $documents->map(function ($d) {
            $collections = $d->groups->map(function ($g) {
                return $g->ancestors()->get()->pluck('name')->merge($g->name)->join('/');
            });

            return $collections->map(function ($c) use ($d) {
                return [
                    $c.'/'.Str::slug($d->title).'.'.Files::extensionFromType($d->file->mime_type),
                    $c.'/'.Str::slug($d->title).'.json',
                ];
            });
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

        $abstract_file_content = $zip->getFromName("project-abstract.txt");

        $zip->close();

        $files = collect([
            'readme.txt',
            "project-abstract.txt",
            "documents.csv",
        ])->merge($files_map);

        $this->assertEquals($files->toArray(), $entries);
        $this->assertEquals($project->description, $abstract_file_content);

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

        $expectedList = [];
        $project->documents()->orderBy('id')->get()->each(function ($d) use (&$expectedList) {
            $this->getFolders($d)->each(function ($f) use ($d, &$expectedList) {
                $expectedList[] = [
                    'id' => $d->uuid,
                    'title' => $d->title,
                    'uploaded_at' => $d->created_at->toDateTimeString(),
                    'file' => $f.'/'.Str::slug($d->title).'.'.Files::extensionFromType($d->file->mime_type),
                    'language' => $d->language,
                    'document_type' => $d->document_type,
                    'uploader' => $d->owner->name,
                    'authors' => $d->authors,
                    'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                    'projects' => $d->projects()->pluck('name')->unique()->join('/'),
                    'collections' => $f,
                    'hash' => $d->hash,
                    'url' => RoutingHelpers::download($d),
                ];
            });
        });

        $this->assertEquals($expectedList, $records);
    }
}
