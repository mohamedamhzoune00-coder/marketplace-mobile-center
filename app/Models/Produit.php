<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Boutique;
use App\Models\Category;
use App\Models\ImagesProduit;
use App\Models\StatistiqueVue;
use App\Models\Signalement;
use App\Models\Demande;

class Produit extends Model
{
    use HasFactory;

    // هاد الأعمدة لي Laravel يقدر يعمرهم عبر create() و update()
    protected $fillable = [
        'boutique_id',
        'category_id',
        'nom',
        'description',
        'prix',
        'stock',
        'marque',
        'modele',
        'disponible',
        'vues',
    ];
    // العلاقة: هاد المنتج كيرجع لبوتيك وحدة برك
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }
    // العلاقة: هاد المنتج تابع لـ Category وحدة برك
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // العلاقة: هاد المنتج عندو بزاف ديال الصور
    public function images()
    {
        return $this->hasMany(ImagesProduit::class);
    }
    // العلاقة: المنتج عندو بزاف ديال الإحصائيات
    public function statistiques()
    {
        return $this->hasMany(StatistiqueVue::class);
    }
    // العلاقة: المنتج يقدر يكون عندو بزاف ديال البلاغات
    public function signalements()
    {
        return $this->hasMany(Signalement::class);
    }
    public function demandes()
{
    return $this->hasMany(Demande::class);
}
}
