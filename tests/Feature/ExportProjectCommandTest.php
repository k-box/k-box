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
        $project = factory(Project::class)->create([
            'name' => 'project / root'
        ]);
        $project->collection->name = $project->name;
        $project->collection->save();

        $documents = [
            factory(DocumentDescriptor::class)->create(['title' => 'Document everywhere']),
            factory(DocumentDescriptor::class)->create(['title' => 'Document 1 - Настройки географического расширения']),
            factory(DocumentDescriptor::class)->create(['title' => 'Document 2']),
            factory(DocumentDescriptor::class)->create(['title' => 'Document 3']),
            factory(DocumentDescriptor::class)->create(['title' => 'Document 4']),
            factory(DocumentDescriptor::class)->create(['title' => 'Document 5']),
            factory(DocumentDescriptor::class)->create(['title' => 'Document 6']),
            factory(DocumentDescriptor::class)->create(['title' => 'Document 7']),
        ];

        $project->collection->documents()->save($documents[0]);
        $project->collection->documents()->save($documents[1]);

        $collection_a = factory(Group::class)->create([
            'name' => 'level 1 - 1',
            'parent_id' => $project->collection->getKey()
        ]);

        $collection_a->documents()->save($documents[2]);
        $collection_a->documents()->save($documents[0]);

        $collection_c = factory(Group::class)->create([
            'name' => 'level 2 - 1',
            'parent_id' => $collection_a->getKey()
        ]);
        $collection_c->documents()->save($documents[3]);
        $collection_c->documents()->save($documents[0]);
        $collection_c->documents()->save($documents[7]);
        
        $collection_d = factory(Group::class)->create([
            'name' => 'level 2 - 2',
            'parent_id' => $collection_a->getKey()
        ]);
        $collection_d->documents()->save($documents[4]);
        $collection_d->documents()->save($documents[0]);

        $collection_b = factory(Group::class)->create([
            'name' => 'level 1 - 2',
            'parent_id' => $project->collection->getKey()
        ]);

        $collection_b->documents()->save($documents[5]);
        $collection_b->documents()->save($documents[0]);
        $collection_b->documents()->save($documents[7]);

        // Second project
        // created only to confirm that the export do not
        // attempt to include other projects in case a
        // document is part of collections under
        // different projects
        $second_project = factory(Project::class)->create([
            'name' => 'project / other'
        ]);
        $second_project->collection->name = $second_project->name;
        $second_project->collection->save();
        $second_project->collection->documents()->save($documents[0]);
        $second_project->collection->documents()->save($documents[6]);

        // project / root [$documents[0], $documents[1]]
        //   'level 1 - 1', [$documents[0], $documents[2]]
        //     'level 2 - 1', [$documents[0], $documents[3], $documents[7]]
        //     'level 2 - 2', [$documents[0], $documents[4]]
        //   'level 1 - 2', [$documents[0], $documents[5], $documents[7]]
        // project / other [$documents[0], $documents[6]]

        return $project;
    }

    private function getFolders(DocumentDescriptor $doc, Project $project)
    {
        return $doc->groups->map(function ($g) use ($project) {
            $ancestors = $g->ancestors()->public()->orderBy('depth', 'desc')->get();

            if (! $ancestors->isEmpty() && ! $ancestors->first()->getProject()->is($project)) {
                return null;
            }
            if ($ancestors->isEmpty() && ! $g->getProject()->is($project)) {
                return null;
            }

            return $ancestors->pluck('name')->merge($g->name)->map(function ($c) {
                return Str::slug($c);
            })->join('/');
        })->filter()->unique();
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

        $records = collect($csv->getRecords())->sortBy('id')->values()->toArray();

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
        $project->documents()->get()->each(function ($d) use (&$expectedList, $project) {
            $this->getFolders($d, $project)->each(function ($f) use ($d, &$expectedList) {
                $expectedList[] = [
                    'id' => $d->uuid,
                    'title' => $d->title,
                    'uploaded_at' => $d->created_at->toDateTimeString(),
                    'file' => $f.'/'.Str::slug($d->title).'.'.Files::extensionFromType($d->file->mime_type),
                    'language' => $d->language,
                    'document_type' => $d->document_type,
                    'uploader' => $d->owner->name,
                    'authors' => $d->authors ?? '',
                    'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                    'projects' => $d->projects()->pluck('name')->unique()->join(' + '),
                    'collections' => $f,
                    'hash' => $d->hash,
                    'url' => RoutingHelpers::download($d),
                ];
            });
        });

        $this->assertEquals(collect($expectedList)->sortBy('id')->values()->toArray(), $records);
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
            'project-abstract.txt',
            'documents.csv',
            "project-root/document-everywhere.txt",
            "project-root/document-everywhere.json",
            "project-root/level-1-1/document-everywhere.txt",
            "project-root/level-1-1/document-everywhere.json",
            "project-root/level-1-1/level-2-1/document-everywhere.txt",
            "project-root/level-1-1/level-2-1/document-everywhere.json",
            "project-root/level-1-1/level-2-2/document-everywhere.txt",
            "project-root/level-1-1/level-2-2/document-everywhere.json",
            "project-root/level-1-2/document-everywhere.txt",
            "project-root/level-1-2/document-everywhere.json",
            "project-root/document-1-nastroiki-geograficeskogo-rassireniya.txt",
            "project-root/document-1-nastroiki-geograficeskogo-rassireniya.json",
            "project-root/level-1-1/document-2.txt",
            "project-root/level-1-1/document-2.json",
            "project-root/level-1-1/level-2-1/document-3.txt",
            "project-root/level-1-1/level-2-1/document-3.json",
            "project-root/level-1-1/level-2-2/document-4.txt",
            "project-root/level-1-1/level-2-2/document-4.json",
            "project-root/level-1-2/document-5.txt",
            "project-root/level-1-2/document-5.json",
            "project-root/level-1-1/level-2-1/document-7.txt",
            "project-root/level-1-1/level-2-1/document-7.json",
            "project-root/level-1-2/document-7.txt",
            "project-root/level-1-2/document-7.json",
        ])->sort()->values()->toArray();

        $this->assertEquals($files, collect($entries)->sort()->values()->toArray());
        $this->assertEquals($project->description, $abstract_file_content);

        $csv = Reader::createFromString($csv_content);
        $csv->setHeaderOffset(0); //set the CSV header offset

        $headers = $csv->getHeader();

        $records = collect($csv->getRecords())->sortBy('id')->values()->toArray();

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
        $project->documents()->orderBy('id')->get()->each(function ($d) use (&$expectedList, $project) {
            $this->getFolders($d, $project)->each(function ($f) use ($d, &$expectedList) {
                $expectedList[] = [
                    'id' => $d->uuid,
                    'title' => $d->title,
                    'uploaded_at' => $d->created_at->toDateTimeString(),
                    'file' => $f.'/'.Str::slug($d->title).'.'.Files::extensionFromType($d->file->mime_type),
                    'language' => $d->language,
                    'document_type' => $d->document_type,
                    'uploader' => $d->owner->name,
                    'authors' => $d->authors ?? '',
                    'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                    'projects' => $d->projects()->pluck('name')->unique()->join(' + '),
                    'collections' => $f,
                    'hash' => $d->hash,
                    'url' => RoutingHelpers::download($d),
                ];
            });
        });

        $this->assertEquals(collect($expectedList)->sortBy('id')->values()->toArray(), $records);
    }
}
