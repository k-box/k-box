<?php

namespace Tests\Feature\Changelogs;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use KBox\Changelog\EntriesFinder;
use Mockery;
use Tests\TestCase;

class ReleaseCommandTest extends TestCase
{
    protected $paths = null;

    protected function tearDown(): void
    {
        parent::tearDown();

        if (! is_null($this->paths)) {
            foreach ($this->paths as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }
        }
    }

    protected function mockEntries($files)
    {
        $mock = Mockery::mock(Filesystem::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $mock->shouldReceive('glob')->andReturn($files);

        $this->app->instance(Filesystem::class, $mock);

        $this->app->instance(EntriesFinder::class, new EntriesFinder($mock));

        return $mock;
    }

    protected function generateTestingEntries()
    {
        $base = base_path('changelogs/unreleased/');

        return [
            $this->generateEntry("$base/100-added.yml", 'added'),
            $this->generateEntry("$base/101-fixed.yml", 'fixed'),
            $this->generateEntry("$base/102-changed.yml", 'changed'),
            $this->generateEntry("$base/103-removed.yml", 'removed'),
            $this->generateEntry("$base/104-deprecated.yml", 'deprecated'),
            $this->generateEntry("$base/105-security.yml", 'security'),
        ];
    }

    protected function generateEntry($path, $type = null)
    {
        $issue = Str::before(basename($path), '-');

        $yaml = <<<"yaml"
        title: "Entry for $type"
        issue: $issue
        merge_request: 2$issue
        author: "@octocat"
        type: $type
        yaml;

        file_put_contents($path, $yaml);

        return $path;
    }
    
    public function test_release_changelog_dry_run()
    {
        $this->paths = $this->generateTestingEntries();

        $this->mockEntries($this->paths);

        $releaseDate = today()->toDateString();

        $expectedMarkdown = <<<"markdown"
        ## [0.32.0] - $releaseDate
        
        ### Added

        - Entry for added by @octocat ([#100](https://github.com/k-box/k-box/issues/100), [#2100](https://github.com/k-box/k-box/pull/2100))
        
        ### Changed

        - Entry for changed by @octocat ([#102](https://github.com/k-box/k-box/issues/102), [#2102](https://github.com/k-box/k-box/pull/2102))
        
        ### Fixed

        - Entry for fixed by @octocat ([#101](https://github.com/k-box/k-box/issues/101), [#2101](https://github.com/k-box/k-box/pull/2101))
        
        ### Security

        - Entry for security by @octocat ([#105](https://github.com/k-box/k-box/issues/105), [#2105](https://github.com/k-box/k-box/pull/2105))
        
        ### Deprecated

        - Entry for deprecated by @octocat ([#104](https://github.com/k-box/k-box/issues/104), [#2104](https://github.com/k-box/k-box/pull/2104))
        
        ### Removed

        - Entry for removed by @octocat ([#103](https://github.com/k-box/k-box/issues/103), [#2103](https://github.com/k-box/k-box/pull/2103))
        
        markdown;

        $this->artisan('release', [
                'version' => 'v0.32.0',
                '--dry-run' => true
            ])
            ->expectsOutput('update changelog.md')
            ->expectsOutput($expectedMarkdown)
            ->assertExitCode(0);

        $this->assertFileExists($this->paths[0]);
    }
    
    public function test_release_changelog_updated()
    {
        $this->paths = $this->generateTestingEntries();

        $this->mockEntries($this->paths);

        $releaseDate = today()->toDateString();

        $expectedMarkdown = <<<"markdown"
        ## [0.32.0] - $releaseDate
        
        ### Added

        - Entry for added by @octocat ([#100](https://github.com/k-box/k-box/issues/100), [#2100](https://github.com/k-box/k-box/pull/2100))
        
        ### Changed

        - Entry for changed by @octocat ([#102](https://github.com/k-box/k-box/issues/102), [#2102](https://github.com/k-box/k-box/pull/2102))
        
        ### Fixed

        - Entry for fixed by @octocat ([#101](https://github.com/k-box/k-box/issues/101), [#2101](https://github.com/k-box/k-box/pull/2101))
        
        ### Security

        - Entry for security by @octocat ([#105](https://github.com/k-box/k-box/issues/105), [#2105](https://github.com/k-box/k-box/pull/2105))
        
        ### Deprecated

        - Entry for deprecated by @octocat ([#104](https://github.com/k-box/k-box/issues/104), [#2104](https://github.com/k-box/k-box/pull/2104))
        
        ### Removed

        - Entry for removed by @octocat ([#103](https://github.com/k-box/k-box/issues/103), [#2103](https://github.com/k-box/k-box/pull/2103))
        
        markdown;

        $this->artisan('release', [
                'version' => 'v0.32.0',
            ])
            ->expectsOutput('update changelog.md')
            ->expectsOutput($expectedMarkdown)
            ->assertExitCode(0);

        $this->assertStringContainsString($expectedMarkdown, file_get_contents(base_path('changelog.md')));

        $this->assertFileDoesNotExist($this->paths[0]);
    }
}
