<?php

namespace KlinkDMS\Contracts;

use Log;
use Closure;
use Exception;

abstract class Action implements Pipe
{

    // protected $canFail = true;

    /**
     * Perform an operation on $content and pass the result onto the next step in the pipeline
     *
     * To pass the $content deeper into the application (allowing the pipe to "pass"), simply call the $next callback with the $content.
     *
     * @param mixed $content The content to elaborate
     * @param \Closure the pointer to the next stage in the pipeline
     * @return mixed the modified content
     * @example
     * public function run($content)
     * {
     *     // Here you perform the task and return the updated $content
     *     // to the next pipe
     *     return $elaborated_content;
     * }
     */
    abstract public function run($content);

    public function handle($content, Closure $next)
    {
        try {
            return $next($this->run($content));
        } catch (Exception $ex) {
            if (property_exists($this, 'canFail') && $this->canFail) {
                Log::error(sprintf('%1$s action error', get_class($this)), ['exception' => $ex, 'input' => $content ]);
                return $next($content);
            }

            throw $ex;
        }
    }
}
