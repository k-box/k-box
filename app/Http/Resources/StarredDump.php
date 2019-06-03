<?php

namespace KBox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StarredDump extends JsonResource
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
            'document' => $this->document->uuid,
            'starred_at' => $this->created_at
        ];
    }
}
