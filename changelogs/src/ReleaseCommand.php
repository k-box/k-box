<?php

namespace KBox\Changelog;

use Illuminate\Console\Command;

class ReleaseCommand extends Command
{
    protected $signature = 'release {version}
                                {--dry-run : Don\'t actually write anything, just print }';

    protected $description = 'Consolidate a release changelog';

    protected static $types = [
        'added' => '### Added',
        'changed' => '### Changed',
        'fixed' => '### Fixed',
        'security' => '### Security',
        'deprecated' => '### Deprecated',
        'removed' => '### Removed',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $finder = $this->laravel->make(EntriesFinder::class);

        $entries = $finder->all();

        $sections = $entries->groupBy('type')->mapWithKeys(function ($entries, $group) {
            $items = $entries->map(function ($entry) {
                return $this->formatEntry($entry);
            })->filter();

            return [$group => $items];
        })->filter();

        $formattedSections = collect(self::$types)->map(function ($title, $key) use ($sections) {
            $content = $sections->get($key);

            if (empty($content)) {
                return null;
            }

            return $title."\n\n".$content->join("\n");
        })->filter()->join("\n\n");

        $version = ltrim($this->argument('version'), 'v');

        $releaseDate = today()->toDateString();

        $releaseMarkdown = <<<"markdown"
        ## [$version] - $releaseDate

        $formattedSections

        markdown;

        $this->info("update changelog.md");
        $this->info('---');
        $this->info($releaseMarkdown);

        if ($this->option('dry-run')) {
            return 0;
        }
        
        $changelog = file_get_contents(base_path('changelog.md'));
        
        $changelog = str_replace('## [Unreleased]', "## [Unreleased]\n\n".$releaseMarkdown, $changelog);

        file_put_contents(base_path('changelog.md'), $changelog);

        $finder->delete();
        
        return 0;
    }

    protected function formatEntry(array $entry)
    {
        $links = array_filter([
            $this->link('issues', $entry['issue'] ?? null),
            $this->link('pull', $entry['merge_request'] ?? null),
        ]);

        $pieces = [
            trim($entry['title']),
            $this->author($entry['author']),
            ! empty($links) ? '('.implode(", ", $links).')' : null,
        ];

        return sprintf('- %1$s', implode(' ', $pieces));
    }

    protected function link($type, $entry)
    {
        if (empty($entry)) {
            return null;
        }

        return "[#$entry](https://github.com/k-box/k-box/$type/$entry)";
    }

    protected function author($value)
    {
        if (empty($value)) {
            return null;
        }

        return "by $value";
    }
}
