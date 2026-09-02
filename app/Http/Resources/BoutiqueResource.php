<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BoutiqueResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'nom'         => $this->nom,
            'description' => $this->description,
            'telephone'   => $this->telephone,
            'email'       => $this->email,
            'adresse'     => $this->adresse,
            'emplacement' => $this->emplacement,
            'logo'        => $this->logo,
            'couverture'  => $this->couverture,
            'actif'       => $this->actif,
            'proprietaire' => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }
}