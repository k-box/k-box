<?php

namespace KBox\Changelog;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class EntriesFinder
{
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    protected function files()
    {
        return $this->filesystem->glob(base_path('changelogs/unreleased/*.yml'));
    }

    public function all()
    {
        $yamlParser = new Parser();

        $files = collect($this->files())->map(function ($file) use ($yamlParser) {
            return $yamlParser->parse($this->filesystem->get($file));
        });

        return $files;
    }

    public function delete()
    {
        $this->filesystem->delete($this->files());
    }
}
