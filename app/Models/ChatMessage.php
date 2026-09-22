<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'role',
        'content',
    ];

    // العلاقة: المحادثة تابع لمستخدم واحد
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}