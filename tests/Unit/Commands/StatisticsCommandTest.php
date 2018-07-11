<?php

namespace Tests\Unit\Commands;

use Artisan;
use KBox\File;
use KBox\User;
use KBox\Group;
use KBox\Project;
use KBox\Shared;
use Carbon\Carbon;
use Tests\TestCase;
use KBox\DocumentDescriptor;
use Tests\Concerns\ClearDatabase;
use KBox\Console\Commands\StatisticsCommand;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StatisticsCommandTest extends TestCase
{
    use ClearDatabase, DatabaseTransactions;

    public function test_overall_report_is_printed()
    {
        $previous_documents = DocumentDescriptor::count();
        $previous_files = File::count();
        $previous_users = User::count();

        factory('KBox\DocumentDescriptor', 6)->create();
        
        $exitCode = Artisan::call('statistics', ['--summary' => true, '--overall' => true]);
        
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode);
        $this->assertTrue(str_contains($output, "Documents (not considering versions) | ".(6 + $previous_documents)));
        $this->assertTrue(str_contains($output, "Uploads                              | ".(6 + $previous_files)));
        $this->assertTrue(str_contains($output, "Registered users                     | ".(6 + $previous_users)));
    }
    
    public function test_report_is_printed()
    {
        $previous_documents = DocumentDescriptor::count();
        $previous_files = File::count();
        $previous_users = User::count();

        factory('KBox\DocumentDescriptor', 6)->create();

        $exitCode = Artisan::call('statistics', ['--summary' => true]);

        $output = Artisan::output();
        
        $this->assertEquals(0, $exitCode);
        $this->assertTrue(str_contains($output, "Documents (not considering versions) | ".(6 + $previous_documents)));
        $this->assertTrue(str_contains($output, "Uploads                              | ".(6 + $previous_files)));
        $this->assertTrue(str_contains($output, "Registered users                     | ".(6 + $previous_users)));
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

    public function test_measurement_values_are_calculated()
    {
        // create the expected dataset
        $users = [
            factory('KBox\User')->create(['created_at' => Carbon::createFromDate(null, 6, 1)]),
            factory('KBox\User')->create(['created_at' => Carbon::createFromDate(null, 6, 5)]),
            factory('KBox\User')->create(['created_at' => Carbon::createFromDate(null, 6, 8)]),
            factory('KBox\User')->create(['created_at' => Carbon::createFromDate(null, 6, 12)]),
        ];

        $files = [
            factory('KBox\File')->create(['created_at' => Carbon::createFromDate(null, 6, 1), 'user_id' => $users[0]->id]),
            factory('KBox\File')->create(['created_at' => Carbon::createFromDate(null, 6, 5), 'user_id' => $users[1]->id]),
            factory('KBox\File')->create(['created_at' => Carbon::createFromDate(null, 6, 8), 'user_id' => $users[2]->id]),
            factory('KBox\File')->create(['created_at' => Carbon::createFromDate(null, 6, 12), 'user_id' => $users[3]->id]),
        ];
        
        $docs = [
            factory('KBox\DocumentDescriptor')->create(['created_at' => Carbon::createFromDate(null, 6, 1), 'file_id' => $files[0]->id, 'owner_id' => $users[0]->id]),
            factory('KBox\DocumentDescriptor')->create(['created_at' => Carbon::createFromDate(null, 6, 5), 'file_id' => $files[1]->id, 'owner_id' => $users[1]->id]),
            factory('KBox\DocumentDescriptor')->create(['created_at' => Carbon::createFromDate(null, 6, 8), 'file_id' => $files[2]->id, 'owner_id' => $users[2]->id]),
            factory('KBox\DocumentDescriptor')->create(['created_at' => Carbon::createFromDate(null, 6, 12), 'file_id' => $files[3]->id, 'owner_id' => $users[3]->id]),
        ];

        $publiclink = factory(Shared::class, 'publiclink')->create([
            'created_at' => Carbon::createFromDate(null, 6, 9),
            'shareable_id' => $docs[0]->id,
            'user_id' => $users[0]->id,
            ]);
        
        $share = factory(Shared::class)->create([
            'created_at' => Carbon::createFromDate(null, 6, 8),
            'shareable_id' => $docs[0]->id,
            'sharedwith_id' => $users[1]->id,
            'user_id' => $users[0]->id,
            ]);
            
        $project = factory(Project::class)->create([
            'created_at' => Carbon::createFromDate(null, 6, 8),
            'user_id' => $users[1]->id,
        ]);

        $today = Carbon::today();

        $from = Carbon::createFromDate(null, 6, 1)->startOfDay();
        $to = Carbon::createFromDate(null, 6, 15)->endOfDay();

        $command = app()->make(StatisticsCommand::class);

        $raw_data = $this->invokePrivateMethod($command, 'generateReport', [$from, $to]);

        $this->assertCount(16, $raw_data);

        $this->assertEquals(['date', 'Users Created', 'Documents Created', 'Documents Created (incl. Trash)', 'Files uploaded' ,'Files uploaded (incl. Trash)', 'Publications performed', 'Projects created', 'Collections created', 'Personal collections created', 'Public Links Created', 'Shares to internal users'], $raw_data[0]);

        $this->assertEquals([date('Y').'-6-1',1,1,1,1,1,0,0,0,0,0,0], $raw_data[1]);
        $this->assertEquals([date('Y').'-6-5',1,1,1,1,1,0,0,0,0,0,0], $raw_data[5]);
        $this->assertEquals([date('Y').'-6-8',1,1,1,1,1,0,1,1,0,0,1], $raw_data[8]);
        $this->assertEquals([date('Y').'-6-9',0,0,0,0,0,0,0,0,0,1,0], $raw_data[9]);
        $this->assertEquals([date('Y').'-6-12',1,1,1,1,1,0,0,0,0,0,0], $raw_data[12]);
    }
}
