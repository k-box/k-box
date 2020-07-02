<?php

namespace KBox\Traits;

trait ScopeNullUuid
{
    
    /**
     * Scope queries to find by empty UUID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithNullUuid($query)
    {
        if (method_exists($this, 'runSoftDelete')) {
            $query->withTrashed();
        }
        return $query->where('uuid', 0)->orWhereNull('uuid');
    }
}
