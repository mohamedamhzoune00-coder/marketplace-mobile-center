<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProduitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'nom'         => $this->nom,
            'description' => $this->description,
            'prix'        => $this->prix,
            'stock'       => $this->stock,
            'marque'      => $this->marque,
            'modele'      => $this->modele,
            'disponible'  => $this->disponible,
            'vues'        => $this->vues,
            'boutique'    => $this->whenLoaded('boutique', function () {
                return [
                    'id'      => $this->boutique->id,
                    'nom'     => $this->boutique->nom,
                    'user_id' => $this->boutique->user_id,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id'  => $this->category->id,
                    'nom' => $this->category->nom,
                ];
            }),
            'images' => $this->whenLoaded('images', function () {
                return ImageResource::collection($this->images);
            }),
        ];
    }
}