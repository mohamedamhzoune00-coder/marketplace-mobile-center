<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Produit;
use App\Models\Boutique;

class Demande extends Model
{
    use HasFactory;

    // هاد الأعمدة لي Laravel يقدر يعمرهم عبر create() و update()
    protected $fillable = [
        'produit_id',
        'boutique_id',
        'nom_client',
        'telephone',
        'email',
        'quantite',
        'message',
        'statut',
    ];

    // العلاقة: الطلب تابع لمنتج واحد
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // العلاقة: الطلب تابع لبوتيك واحد
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }
    
}