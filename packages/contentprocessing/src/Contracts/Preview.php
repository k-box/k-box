<?php

namespace Content\Contracts;

/**
 * Preview interface.
 *
 * Define what methods must be exposed by a preview generation class.
 * A preview class converts the content of a specific file type to HTML
 */
interface Preview extends FileLoader
{

    /**
     * Convert the file content to HTML
     *
     * @return string|stream the HTML for the preview
     */
    public function html();

    /**
     * The eventual CSS needed to properly render the preview
     *
     * @return string|stream|null the additional CSS needed for rendering the preview. Return null if additional CSS is not needed.
     */
    public function css();
}
