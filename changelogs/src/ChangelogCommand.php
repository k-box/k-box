<?php

namespace KBox\Changelog;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ChangelogCommand extends Command
{
    protected $signature = 'changelog {title?}
                                {--f|force : Overwrite entry if already exists}
                                {--dry-run : Don\'t actually write anything, just print }
                                {--i|issue= : Set the issue number}
                                {--m|merge-request= : Set the merge request number}
                                {--u|author= : Set the author}
                                {--t|type= : Set the entry type}';

    protected $description = 'Generate a changelog entry file';

    protected static $types = [
        'added',
        'fixed',
        'changed',
        'deprecated',
        'removed',
        'security',
    ];

    protected static $categories = [
        'New feature' => 'added',
        'Bug fix' => 'fixed',
        'Feature change' => 'changed',
        'New deprecation' => 'deprecated',
        'Feature removal' => 'removed',
        'Security fix' => 'security',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $git = $this->laravel->make(Git::class);

        $branch = Str::slug($git->branch());

        $path = "changelogs/unreleased/$branch.yml";
        $file = base_path($path);

        $force = $this->option('force') ?? false;

        if (is_file($file) && ! $force) {
            $this->error("error $path already exists! Use `--force` to overwrite.");
            return 127;
        }

        $commit = $git->commit();

        $title = $this->argument('title') ?? $this->ask('Please specify a title for the changelog entry', $commit) ?? $commit;
        $type = $this->ensureIsType($this->option('type') ?? $this->choice('Please specify the category of your change', array_keys(self::$categories)));
        $mergeRequest = $this->option('merge-request') ?? $this->ask('The merge request identifier', $this->getMergeFromCommit($commit)) ?? $this->getMergeFromCommit($commit);

        $defaultIssueNumber = $this->getIssueNumberFromBranch($branch) ?? $this->getIssueFromCommit($commit);

        $issue = $this->option('issue') ?? $this->ask('The issue number', $defaultIssueNumber) ?? $defaultIssueNumber;
        $author = $this->ensureAuthorStartsWithAt($this->option('author') ?? $this->ask('Please specify the GitHub username of the author'));

        $yaml = <<<"yaml"
        title: $title
        issue: $issue
        merge_request: $mergeRequest
        author: $author
        type: $type
        yaml;

        $this->info("create $path");
        $this->info('---');
        $this->info($yaml);

        if ($this->option('dry-run')) {
            return 0;
        }

        file_put_contents($file, $yaml);

        return 0;
    }

    protected function ensureIsType($value)
    {
        if (in_array($value, array_keys(self::$categories))) {
            return self::$categories[$value];
        }

        if (! in_array($value, self::$types)) {
            throw new InvalidArgumentException("Unexpected type. Found [$value], expected one of [].");
        }

        return $value;
    }

    protected function getIssueNumberFromBranch($branch)
    {
        preg_match('/^(\d+)-/', $branch, $matches);

        return $matches[1] ?? null;
    }

    protected function getIssueFromCommit($commit)
    {
        preg_match('/#(\d+)/', $commit, $matches);

        return $matches[1] ?? null;
    }

    protected function getMergeFromCommit($commit)
    {
        preg_match('/!(\d+)/', $commit, $matches);

        return $matches[1] ?? null;
    }

    protected function ensureAuthorStartsWithAt($author)
    {
        if (empty($author)) {
            return null;
        }
        return '@'.ltrim($author, '@');
    }
}
