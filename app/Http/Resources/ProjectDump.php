<?php

namespace KBox\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectDump extends JsonResource
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
            'description' => $this->description,
            'created_at' => $this->created_at,
            'documents' => $this->documents()->get()->map->uuid,
            'collections' => CollectionDump::collection($this->collection->getDescendants()),
            'microsite' => optional($this->microsite)->makeHidden('attribute')
                ->makeHidden('id')
                ->makeHidden('project_id')
                ->makeHidden('user_id')
                ->makeHidden('updated_at')
                ->makeHidden('deleted_at')->toJson()
        ];
    }
}
