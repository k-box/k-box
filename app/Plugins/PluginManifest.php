<?php

namespace KBox\Plugins;

use Illuminate\Foundation\PackageManifest;

/**
 * The discovered plugins manifest
 */
class PluginManifest extends PackageManifest
{
    /**
     * The list of plugins found
     *
     * @return \Illuminate\Support\Collection
     */
    public function plugins()
    {
        return collect($this->getManifest())->filter()->map(function ($manifest) {
            return new Manifest($manifest);
        });
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $packages = [];

        $composerFiles = $this->files->glob(rtrim($this->basePath, '/').'/**/composer.json');

        if (! empty($composerFiles)) {
            $decoded = null;
            foreach ($composerFiles as $composerFile) {
                $decoded = json_decode($this->files->get($composerFile), true);
                if ($decoded && $decoded['type'] === 'kbox-plugin') {
                    $packages[] = collect($decoded)->merge(['path' => $this->files->dirname($composerFile)]);
                }
            }
        }

        $ignoreAll = in_array('*', $ignore = $this->packagesToIgnore());

        $this->write(collect($packages)->mapWithKeys(function ($package) {
            return [$this->format($package->get('name')) => $package->only(['description', 'authors', 'license', 'path'])
                    ->merge(['name' => $this->format($package->get('name'))])
                    ->merge($package->get('extra')['kbox'])->all() ?? []];
        })->each(function ($configuration) use (&$ignore) {
            $ignore = array_merge($ignore, $configuration['dont-discover'] ?? []);
        })->reject(function ($configuration, $package) use ($ignore, $ignoreAll) {
            return $ignoreAll || in_array($package, $ignore);
        })->filter()->all());
    }

    /**
     * Format the given package name.
     *
     * @param  string  $package
     * @return string
     */
    protected function format($package)
    {
        return str_slug(str_replace('/', '-', $package));
    }

    /**
     * Get all of the package names that should be ignored.
     *
     * @return array
     */
    protected function packagesToIgnore()
    {
        return [];
    }
}
