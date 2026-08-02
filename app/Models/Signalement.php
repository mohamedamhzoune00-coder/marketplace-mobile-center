<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Produit;

class Signalement extends Model
{
    use HasFactory;

    // هاد الأعمدة لي Laravel يقدر يعمرهم عبر create() و update()
    protected $fillable = [
        'user_id',
        'produit_id',
        'raison',
        'statut',
    ];

    // العلاقة: هاد البلاغ تابع لمستخدم واحد
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة: هاد البلاغ تابع لمنتج واحد
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}