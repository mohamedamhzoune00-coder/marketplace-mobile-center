<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;

class ImagesProduit extends Model
{
    use HasFactory;

    // هاد الأعمدة لي Laravel يقدر يعمرهم عبر create() و update()
    protected $fillable = [
        'produit_id',
        'chemin',
        'principale',
        'ordre',
    ];
    // العلاقة: هاد الصورة كترجع لمنتج وحيد
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}