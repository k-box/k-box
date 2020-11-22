<?php

namespace Tests\Feature\Changelogs;

use KBox\Changelog\Git;
use Mockery;
use Tests\TestCase;

class ChangelogCommandTest extends TestCase
{
    protected $path = null;

    protected function tearDown(): void
    {
        parent::tearDown();

        if (! is_null($this->path) && is_file($this->path)) {
            unlink($this->path);
        }
    }

    protected function mockGit($branch, $commit)
    {
        $mock = Mockery::mock(Git::class);

        $mock->shouldReceive('branch')->andReturn($branch);
        $mock->shouldReceive('commit')->andReturn($commit);

        $this->app->instance(Git::class, $mock);
    }
    
    public function test_changelog_entry_written()
    {
        $this->mockGit('100-test-branch', 'Commit description');

        $this->path = base_path('changelogs/unreleased/100-test-branch.yml');

        $expectedYaml = <<<'yaml'
        title: Test add entry!
        issue: 100
        merge_request: 
        author: @octocat
        type: changed
        yaml;

        $this->artisan('changelog', [
                'title' => 'Test add entry!'
            ])
            ->expectsQuestion('Please specify the category of your change', 'Feature change')
            ->expectsQuestion('The merge request identifier', '')
            ->expectsQuestion('The issue number', '100')
            ->expectsQuestion('Please specify the GitHub username of the author', 'octocat')
            ->expectsOutput('create changelogs/unreleased/100-test-branch.yml')
            ->expectsOutput($expectedYaml)
            ->assertExitCode(0);

        $this->assertFileExists($this->path);

        $this->assertEquals($expectedYaml, file_get_contents($this->path));
    }

    public function test_changelog_entry_overwrite_denied_without_force()
    {
        $this->mockGit('100-test-branch', 'Commit description');

        $this->path = base_path('changelogs/unreleased/100-test-branch.yml');
        file_put_contents($this->path, 'test');

        $this->artisan('changelog', [
                'title' => 'Test add entry!'
            ])
            ->expectsOutput('error changelogs/unreleased/100-test-branch.yml already exists! Use `--force` to overwrite.')
            ->assertExitCode(127);
    }

    public function test_changelog_entry_overwritten()
    {
        $this->mockGit('100-test-branch', 'Commit description');

        $this->path = base_path('changelogs/unreleased/100-test-branch.yml');
        file_put_contents($this->path, 'test');

        $expectedYaml = <<<'yaml'
        title: Test add entry!
        issue: 100
        merge_request: 
        author: @octocat
        type: changed
        yaml;

        $this->artisan('changelog', [
                'title' => 'Test add entry!',
                '--force' => true,
            ])
            ->expectsQuestion('Please specify the category of your change', 'Feature change')
            ->expectsQuestion('The merge request identifier', '')
            ->expectsQuestion('The issue number', '100')
            ->expectsQuestion('Please specify the GitHub username of the author', 'octocat')
            ->expectsOutput('create changelogs/unreleased/100-test-branch.yml')
            ->expectsOutput($expectedYaml)
            ->assertExitCode(0);

        $this->assertFileExists($this->path);

        $this->assertEquals($expectedYaml, file_get_contents($this->path));
    }
    
    public function test_title_asked_when_not_specified()
    {
        $this->mockGit('100-test-branch', 'Commit description');

        $this->path = base_path('changelogs/unreleased/100-test-branch.yml');

        $expectedYaml = <<<'yaml'
        title: Test add entry!
        issue: 100
        merge_request: 
        author: @octocat
        type: changed
        yaml;

        $this->artisan('changelog')
            ->expectsQuestion('Please specify a title for the changelog entry', 'Test add entry!')
            ->expectsQuestion('Please specify the category of your change', 'Feature change')
            ->expectsQuestion('The merge request identifier', '')
            ->expectsQuestion('The issue number', '100')
            ->expectsQuestion('Please specify the GitHub username of the author', 'octocat')
            ->expectsOutput('create changelogs/unreleased/100-test-branch.yml')
            ->expectsOutput($expectedYaml)
            ->assertExitCode(0);

        $this->assertFileExists($this->path);

        $this->assertEquals($expectedYaml, file_get_contents($this->path));
    }

    public function test_default_values_used_without_answers()
    {
        $this->mockGit('100-test-branch', 'Commit description (#161) (!162)');

        $this->path = base_path('changelogs/unreleased/100-test-branch.yml');

        $expectedYaml = <<<'yaml'
        title: Commit description (#161) (!162)
        issue: 100
        merge_request: 162
        author: 
        type: changed
        yaml;

        $this->artisan('changelog', ['--type' => 'changed'])
            ->expectsQuestion('Please specify a title for the changelog entry', null)
            ->expectsQuestion('The merge request identifier', null)
            ->expectsQuestion('The issue number', null)
            ->expectsQuestion('Please specify the GitHub username of the author', null)
            ->expectsOutput('create changelogs/unreleased/100-test-branch.yml')
            ->expectsOutput($expectedYaml)
            ->assertExitCode(0);

        $this->assertFileExists($this->path);

        $this->assertEquals($expectedYaml, file_get_contents($this->path));
    }

    public function test_issue_number_read_from_commit_message()
    {
        $this->mockGit('test-branch', 'Commit description (#161) (!162)');

        $this->path = base_path('changelogs/unreleased/test-branch.yml');

        $expectedYaml = <<<'yaml'
        title: Commit description (#161) (!162)
        issue: 161
        merge_request: 162
        author: 
        type: changed
        yaml;

        $this->artisan('changelog', ['--type' => 'changed'])
            ->expectsQuestion('Please specify a title for the changelog entry', null)
            ->expectsQuestion('The merge request identifier', null)
            ->expectsQuestion('The issue number', null)
            ->expectsQuestion('Please specify the GitHub username of the author', null)
            ->expectsOutput('create changelogs/unreleased/test-branch.yml')
            ->expectsOutput($expectedYaml)
            ->assertExitCode(0);

        $this->assertFileExists($this->path);

        $this->assertEquals($expectedYaml, file_get_contents($this->path));
    }

    public function test_changelog_accept_options()
    {
        $this->mockGit('100-test-branch', 'Commit description');

        $this->path = base_path('changelogs/unreleased/100-test-branch.yml');

        $expectedYaml = <<<'yaml'
        title: A title
        issue: 101
        merge_request: 102
        author: @octocat
        type: changed
        yaml;

        $this->artisan('changelog', [
            'title' => 'A title',
            '--type' => 'changed',
            '-i' => '101',
            '-m' => '102',
            '-u' => 'octocat',
            ])
            ->expectsOutput('create changelogs/unreleased/100-test-branch.yml')
            ->expectsOutput($expectedYaml)
            ->assertExitCode(0);

        $this->assertFileExists($this->path);

        $this->assertEquals($expectedYaml, file_get_contents($this->path));
    }
}
