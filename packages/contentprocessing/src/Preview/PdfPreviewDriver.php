<?php

namespace KBox\Documents\Preview;

use KBox\File;
use Illuminate\Contracts\Support\Renderable;

/**
 * PDF preview.
 */
class PdfPreviewDriver extends BasePreviewDriver implements Renderable
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
        return view('preview::type.pdf', $this->view_data)->render();
    }

    public function supportedMimeTypes()
    {
        return [
            'application/pdf'
        ];
    }
}
