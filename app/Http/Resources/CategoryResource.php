<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'nom'         => $this->nom,
            'description' => $this->description,
            'parent_id'   => $this->parent_id,
            'ordre'       => $this->ordre,
            'actif'       => $this->actif,
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id'  => $this->parent->id,
                    'nom' => $this->parent->nom,
                ];
            }),
            'children' => $this->whenLoaded('children', function () {
                return CategoryResource::collection($this->children);
            }),
        ];
    }
}