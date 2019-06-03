<?php

namespace KBox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentDump extends JsonResource
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
            'uuid' => $this->uuid,
            'title' => $this->title,
            'name' => $this->file->name,
            'hash' => $this->hash,
            'size' => $this->file->size,
            'mime_type' => $this->mime_type,
            'language' => $this->language,
            'abstract' => $this->abstract,
            'authors' => $this->authors,
            'created_at' => $this->created_at,
            'copyright_usage' => $this->copyright_usage,
            'copyright_owner' => $this->copyright_owner,
            'properties' => $this->file->properties,
        ];
    }
}
