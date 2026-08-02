<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;

class StatistiqueVue extends Model
{
    use HasFactory;

    // هاد الأعمدة لي Laravel يقدر يعمرهم عبر create() و update()
    protected $fillable = [
        'produit_id',
        'date',
        'nombre_vues',
    ];

    // العلاقة: هاد الإحصائية تابعة لمنتج واحد
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}