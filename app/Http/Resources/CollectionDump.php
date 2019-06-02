<?php

namespace KBox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionDump extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'created_at' => $this->created_at,
            'parent_id' => $this->parent_id,
        ];
    }
}
