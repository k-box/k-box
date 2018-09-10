<?php

namespace KBox\Documents\Contracts;

use Illuminate\Contracts\Support\Renderable;

/**
 * Preview interface.
 *
 * Define what methods must be exposed by a preview class.
 * A preview class contain the result of a preview generation action
 */
interface Previewable extends Renderable
{
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();
}
