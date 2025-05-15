<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriberJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'status',
        'user_id',
        'created_by',
        'deleted_by',
        'is_sent',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
