<?php

namespace Tests\Unit\Commands;

use Artisan;
use KBox\File;
use KBox\User;
use KBox\Group;
use KBox\Project;
use Tests\TestCase;
use KBox\DocumentDescriptor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StatisticsCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_overall_report_is_printed()
    {
        $previous_documents = DocumentDescriptor::count();
        $previous_files = File::count();
        $previous_users = User::count();

        $exitCode = Artisan::call('statistics', ['--summary' => true, '--overall' => true]);

        $this->assertEquals(0, $exitCode);
    }
    
    public function test_report_is_printed()
    {
        $previous_documents = DocumentDescriptor::count();
        $previous_files = File::count();
        $previous_users = User::count();

        $exitCode = Artisan::call('statistics', ['--summary' => true]);

        $this->assertEquals(0, $exitCode);
    }

    public function test_influx_report_is_printed()
    {
        $previous_documents = DocumentDescriptor::count();
        $previous_files = File::count();
        $previous_users = User::count();
        $previous_projects = Project::count();
        $previous_collections = Group::count();
        $previous_personal_collections = Group::where('is_private', true)->count();

        $exitCode = Artisan::call('statistics', ['--summary' => true, '--overall' => true, '--influx' => true]);

        $this->assertEquals(0, $exitCode);

        $output = Artisan::output();

        $this->assertEquals(
            sprintf(
                'kbox,domain=%1$s documents=%2$si,uploads=%3$si,published=%4$si,users=%5$si,projects=%6$si,collections=%7$si,personal_collections=%8$si',
                url('/'),
                $previous_documents,
                $previous_files,
                0,
                $previous_users,
                $previous_projects,
                $previous_collections,
                $previous_personal_collections
            ),
            trim($output));
    }
}
