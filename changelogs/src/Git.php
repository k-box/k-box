<?php

namespace KBox\Changelog;

class Git
{
    /**
     * Get current branch
     *
     * @return string
     */
    public function branch()
    {
        exec('git symbolic-ref --short HEAD', $output);

        return $output[0] ?? null;
    }
    
    /**
     * Get current commit message
     *
     * @return string
     */
    public function commit()
    {
        exec('git log --format=%s -1', $output);

        return $output[0] ?? null;
    }
}
