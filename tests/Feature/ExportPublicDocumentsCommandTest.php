<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use KBox\DocumentDescriptor;
use KBox\Project;
use KBox\Publication;
use KBox\RoutingHelpers;
use KBox\User;
use League\Csv\Reader;

class ExportPublicDocumentsCommandTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_csv_with_document_listing_is_generated()
    {
        Storage::fake('app');

        $user = factory(User::class)->create();

        $privateDocuments = factory(DocumentDescriptor::class, 3)->create();
        $publicDocuments = factory(DocumentDescriptor::class, 3)
            ->create(['is_public' => true])
            ->each(function ($document) use ($user) {
                $document->publications()->save(new Publication([
                    'published_by' => $user->getKey(),
                    'published_at' => now(),
                    'pending' => false,
                ]));
            });

        $date = Carbon::today()->toDateString();

        $exitCode = Artisan::call('export:published', [
            '--only-list' => true
        ]);

        $this->assertEquals(0, $exitCode);

        $expectedFile = "publications-$date.csv";
        
        Storage::disk('app')->assertExists($expectedFile);

        $csv = Reader::createFromPath(Storage::disk('app')->path($expectedFile), 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset

        $headers = $csv->getHeader();

        $records = collect($csv->getRecords())->values()->toArray();

        $this->assertEquals([
            'id',
            'title',
            'file',
            'publication_date',
            'license',
            'projects',
            'collections',
            'hash',
            'url',
        ], $headers);

        $this->assertEquals($publicDocuments->map(function ($d) {
            return [
                'id' => $d->uuid,
                'title' => $d->title,
                'file' => $d->file->path,
                'publication_date' => $d->publication()->published_at->toDateTimeString(),
                'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                'projects' => '',
                'collections' => '',
                'hash' => $d->hash,
                'url' => RoutingHelpers::download($d),
            ];
        })->toArray(), $records);
    }

    public function test_csv_includes_projects_and_collections()
    {
        Storage::fake('app');

        $user = factory(User::class)->create();
        $project = factory(Project::class)->create(['user_id' => $user->id]);
        $project2 = factory(Project::class)->create(['user_id' => $user->id]);

        $privateDocuments = factory(DocumentDescriptor::class, 3)->create();
        $publicDocuments = factory(DocumentDescriptor::class, 3)
            ->create(['is_public' => true])
            ->each(function ($document) use ($user, $project, $project2) {
                $document->publications()->save(new Publication([
                    'published_by' => $user->getKey(),
                    'published_at' => now(),
                    'pending' => false,
                ]));

                $document->groups()->save($project->collection, ['added_by' => $user->getKey()]);
                $document->groups()->save($project2->collection, ['added_by' => $user->getKey()]);
            });

        $date = Carbon::today()->toDateString();

        $exitCode = Artisan::call('export:published', [
            '--only-list' => true
        ]);

        $this->assertEquals(0, $exitCode);

        $expectedFile = "publications-$date.csv";
        
        Storage::disk('app')->assertExists($expectedFile);

        $csv = Reader::createFromPath(Storage::disk('app')->path($expectedFile), 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset

        $headers = $csv->getHeader();

        $records = collect($csv->getRecords())->values()->toArray();

        $this->assertEquals([
            'id',
            'title',
            'file',
            'publication_date',
            'license',
            'projects',
            'collections',
            'hash',
            'url',
        ], $headers);

        $this->assertEquals($publicDocuments->map(function ($d) {
            return [
                'id' => $d->uuid,
                'title' => $d->title,
                'file' => $d->file->path,
                'publication_date' => $d->publication()->published_at->toDateTimeString(),
                'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                'projects' => $d->projects()->pluck('name')->join('.'),
                'collections' => $d->groups()->public()->pluck('name')->join('.'),
                'hash' => $d->hash,
                'url' => RoutingHelpers::download($d),
            ];
        })->toArray(), $records);
    }

    public function test_public_document_export_include_files()
    {
        Storage::fake('app');

        $user = factory(User::class)->create();

        $privateDocuments = factory(DocumentDescriptor::class, 3)->create();
        $publicDocuments = factory(DocumentDescriptor::class, 3)
            ->create(['is_public' => true])
            ->each(function ($document) use ($user) {
                $document->publications()->save(new Publication([
                    'published_by' => $user->getKey(),
                    'published_at' => now(),
                    'pending' => false,
                ]));
            });

        $date = Carbon::today()->toDateString();

        $exitCode = Artisan::call('export:published');

        $this->assertEquals(0, $exitCode);

        $expectedFile = "publications-$date.zip";
        
        Storage::disk('app')->assertExists($expectedFile);

        $csv = Reader::createFromPath(Storage::disk('app')->path($expectedFile), 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset

        $headers = $csv->getHeader();

        $records = collect($csv->getRecords())->values()->toArray();

        $this->assertEquals([
            'id',
            'title',
            'file',
            'publication_date',
            'license',
            'projects',
            'collections',
            'hash',
            'url',
        ], $headers);

        $this->assertEquals($publicDocuments->map(function ($d) {
            return [
                'id' => $d->uuid,
                'title' => $d->title,
                'file' => $d->file->path,
                'publication_date' => $d->publication()->published_at->toDateTimeString(),
                'license' => optional($d->copyright_usage)->name ?? 'Copyright',
                'projects' => '',
                'collections' => '',
                'hash' => $d->hash,
                'url' => RoutingHelpers::download($d),
            ];
        })->toArray(), $records);
    }
}
