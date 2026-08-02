<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Boutique;
class HorairesBoutique extends Model
{
    use HasFactory;
    protected $table = 'horaires_boutique';
    // هاد الأعمدة لي Laravel يقدر يعمرهم عبر create() و update()
    protected $fillable = [
        'boutique_id',
        'jour',
        'heure_ouverture',
        'heure_fermeture',
        'ferme',
    ];
    // العلاقة: هاد الساعات كترجع لبوتيك واحد
public function boutique()
{
    return $this->belongsTo(Boutique::class);
}
}
