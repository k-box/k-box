<?php

namespace Tests\Unit\Documents;

use KBox\File;
use Tests\TestCase;
use KBox\Jobs\ThumbnailGenerationJob;
use KBox\Documents\Facades\Thumbnails;
use KBox\Documents\Services\ThumbnailsService;

class ThumbnailGenerationJobTest extends TestCase
{
    public function test_job_calls_thumbnails_generate()
    {
        Thumbnails::fake();

        $file = factory(File::class)->make([
            'path' => __DIR__.'/../../data/project-avatar.png',
            'mime_type' => 'image/png'
        ]);

        (new ThumbnailGenerationJob($file))->handle(app(ThumbnailsService::class));

        Thumbnails::assertGenerateCalled($file);
    }
}
