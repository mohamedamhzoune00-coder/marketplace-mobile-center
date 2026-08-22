<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'produit_id',
        'boutique_id',
        'nom_client',
        'telephone',
        'email',
        'quantite',
        'message',
        'statut',
        'token',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($demande) {
            $demande->token = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }
}