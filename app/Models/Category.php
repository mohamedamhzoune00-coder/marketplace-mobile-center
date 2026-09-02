<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;

class Category extends Model
{
    use HasFactory;

    // هاد الأعمدة مسموح لـ Laravel يعمرهم باستعمال create() و update()
    protected $fillable = [

        'nom',
        'description',
        'parent_id',
        'ordre',
        'actif',

    ];

    // العلاقة: Category وحدة فيها بزاف ديال المنتجات
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
    // العلاقة: الفئات الفرعية التابعة لهاد الفئة
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // العلاقة: الفئة الأب
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}