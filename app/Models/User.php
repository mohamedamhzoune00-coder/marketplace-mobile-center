<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Boutique;
use App\Models\Signalement;
use App\Models\JournalAudit;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'actif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // العلاقة: المستخدم عندو Boutique وحدة
    public function boutique()
    {
        return $this->hasOne(Boutique::class);
    }

    // العلاقة: المستخدم يقدر يدير بزاف ديال البلاغات
    public function signalements()
    {
        return $this->hasMany(Signalement::class);
    }

    // العلاقة: المستخدم عندو بزاف ديال العمليات
    public function journauxAudit()
    {
        return $this->hasMany(JournalAudit::class);
    }
}