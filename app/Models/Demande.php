<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'produit_id',
        'boutique_id',
        'nom_client',
        'telephone',
        'email',
        'quantite',
        'message',
        'statut',
    ];

    // الطلب تابع للمستخدم لي دارو
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // الطلب تابع لمنتج واحد
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // الطلب تابع لبوتيك واحد
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }
}