<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DemandeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'nom_client' => $this->nom_client,
            'telephone'  => $this->telephone,
            'email'      => $this->email,
            'quantite'   => $this->quantite,
            'message'    => $this->message,
            'statut'     => $this->statut,
            'created_at' => $this->created_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'produit' => $this->whenLoaded('produit', function () {
                return [
                    'id'   => $this->produit->id,
                    'nom'  => $this->produit->nom,
                    'prix' => $this->produit->prix,
                ];
            }),
            'boutique' => $this->whenLoaded('boutique', function () {
                return [
                    'id'  => $this->boutique->id,
                    'nom' => $this->boutique->nom,
                ];
            }),
        ];
    }
}