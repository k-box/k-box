<?php

namespace KBox\Documents\Preview;

use KBox\File;
use Illuminate\Contracts\Support\Renderable;

/**
 * Map based preview.
 */
class MapPreviewDriver extends BasePreviewDriver implements Renderable
{
    const DEFAULT_PROVIDERS = [
        "osm" => [
            'type' => 'tile',
            'label' => "Open Street Maps",
            'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            'maxZoom' => 19,
        ],
    ];

    const DEFAULT_PROVIDER = "Open Street Maps";

    protected $view_data = [];

    public function preview(File $file) : Renderable
    {
        $this->view_data['file'] = $file;
        $this->view_data['providers'] = self::DEFAULT_PROVIDERS;
        $this->view_data['defaultProvider'] = self::DEFAULT_PROVIDER;
        return $this;
    }

    public function with($data)
    {
        $this->view_data = array_merge($this->view_data, array_wrap($data));
        return $this;
    }

    public function render()
    {
        return view('preview::type.map', $this->view_data)->render();
    }

    public function supportedMimeTypes()
    {
        return [
            'application/geo+json',
        ];
    }
}
