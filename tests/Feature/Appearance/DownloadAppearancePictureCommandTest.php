<?php

namespace Tests\Feature\Appearance;

use Illuminate\Support\Facades\Bus;
use KBox\Jobs\DownloadAppearancePicture;
use Queue;
use Tests\TestCase;

class DownloadAppearancePictureCommandTest extends TestCase
{
    public function test_command_dispatches_job()
    {
        Queue::fake();

        $this->artisan('appearance:downloadpicture')
            ->expectsOutput("Picture download dispatched")
            ->assertExitCode(0);

        Queue::assertPushed(DownloadAppearancePicture::class, function ($job) {
            return $job->force === false && $job->picture == config('appearance.picture');
        });
    }

    public function test_force_option_is_available()
    {
        Queue::fake();

        $this->artisan('appearance:downloadpicture --force')
            ->expectsOutput("Picture download dispatched")
            ->assertExitCode(0);

        Queue::assertPushed(DownloadAppearancePicture::class, function ($job) {
            return $job->force === true && $job->picture == config('appearance.picture');
        });
    }

    public function test_picture_option_is_available()
    {
        Queue::fake();
        
        $picture = 'https://images.unsplash.com/photo-1596618986211-c52f015773ae?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=80';

        $this->artisan("appearance:downloadpicture --picture $picture")
            ->expectsOutput("Picture download dispatched")
            ->assertExitCode(0);

        Queue::assertPushed(DownloadAppearancePicture::class, function ($job) use ($picture) {
            return $job->force === false && $job->picture == $picture;
        });
    }

    public function test_now_option_is_available()
    {
        Bus::fake();
        
        $this->artisan("appearance:downloadpicture --now")
            ->expectsOutput("Picture download dispatched")
            ->assertExitCode(0);

        Bus::assertDispatched(DownloadAppearancePicture::class, function ($job) {
            return $job->force === false && $job->picture == config('appearance.picture');
        });
    }
}
