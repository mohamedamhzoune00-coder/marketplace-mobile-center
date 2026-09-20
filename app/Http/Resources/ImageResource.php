<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
       return [
    'id'         => $this->id,
    'chemin'     => $this->chemin,
    'url'        => asset('storage/' . $this->chemin),
    'principale' => (bool) $this->principale,
    'ordre'      => (int) $this->ordre,
    'produit_id' => $this->produit_id,
    'created_at' => $this->created_at,
    'updated_at' => $this->updated_at,
];
    }
}