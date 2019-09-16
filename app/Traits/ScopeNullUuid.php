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
        return $query->withTrashed()
                    //  ->whereUuid("00000000-0000-0000-0000-000000000000")
                     ->where('uuid', 0);
    }
}
