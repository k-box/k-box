<?php


namespace KBox\Plugins;

use Illuminate\Support\Fluent;

/**
 * The plugin manifest
 *
 * @property string $name The plugin name
 * @property string $description The plugin description
 * @property string $license The plugin license
 * @property string $path The plugin path on disk
 * @property array $authors The plugin author list
 * @property array $providers The list of defined Plugin service providers
 */
final class Manifest extends Fluent
{
    /**
     * Create a new manifest instance.
     *
     * @param  array    $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct(array_merge($attributes, [
            'enabled' => false,
            'configuration' => [],
        ]));
    }
}
