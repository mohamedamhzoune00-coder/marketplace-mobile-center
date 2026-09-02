<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SignalementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'raison' => $this->raison,
            'statut' => $this->statut,
            'created_at' => $this->created_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'produit' => $this->whenLoaded('produit', function () {
                return [
                    'id'  => $this->produit->id,
                    'nom' => $this->produit->nom,
                ];
            }),
        ];
    }
}