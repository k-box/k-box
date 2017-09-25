<?php

namespace KlinkDMS\Contracts;

use Closure;

interface Pipe
{
    /**
     * Perform an operation on $content and pass the result onto the next step in the pipeline
     *
     * @param mixed $content The content to elaborate
     * @param \Closure the pointer to the next stage in the pipeline
     * @return mixed the invocation to the next Pipe in the Pipeline
     * @example
     * public function handle($content, $next)
     * {
     *     // Here you perform the task and return the updated $content
     *     // to the next pipe
     *     return $next($elaborated_content);
     * }
     */
     public function handle($descriptor, Closure $next);
}
