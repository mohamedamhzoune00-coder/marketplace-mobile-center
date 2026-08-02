<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class JournalAudit extends Model
{
    use HasFactory;
    protected $table = 'journaux_audit';
    // هاد الأعمدة لي Laravel يقدر يعمرهم عبر create() و update()
    protected $fillable = [
        'user_id',
        'action',
        'table_concernee',
        'record_id',
        'details',
        'ip_address',
        'user_agent',
    ];

    // العلاقة: هاد العملية تابعة لمستخدم واحد
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
