<?php

namespace KBox\Documents\Preview;

use KBox\File;
use Illuminate\Contracts\Support\Renderable;

/**
 * Image preview.
 */
class ImagePreviewDriver extends BasePreviewDriver implements Renderable
{
    private $view_data = [];

    public function preview(File $file) : Renderable
    {
        $this->view_data['file'] = $file;
        return $this;
    }

    public function with($data)
    {
        $this->view_data = array_merge($this->view_data, array_wrap($data));
        return $this;
    }

    public function render()
    {
        return view('preview::type.image', $this->view_data)->render();
    }

    public function supportedMimeTypes()
    {
        return [
            'image/jpeg',
            'image/gif',
            'image/png',
        ];
    }
}
