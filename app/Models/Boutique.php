<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\HorairesBoutique;
use App\Models\Demande;
use App\Models\Produit;

class Boutique extends Model
{
    use HasFactory;

    // هاد الأعمدة مسموح لـ Laravel يعمرهم باستعمال create() و update()
    protected $fillable = [

        // هاد الأعمدة مسموح لـ Laravel يعمرهم
        'user_id',
        'nom',
        'description',
        'telephone',
        'email',
        'adresse',
        'emplacement',
        'logo',
        'couverture',
        'actif',

    ];

    // العلاقة: هاد البوتيك تابع لمستخدم واحد
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة: البوتيك عندو بزاف ديال أوقات العمل
    public function horaires()
    {
        return $this->hasMany(HorairesBoutique::class);
    }

    // العلاقة: البوتيك عندها بزاف ديال الطلبات
    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    // العلاقة: البوتيك عندها بزاف ديال المنتجات
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}