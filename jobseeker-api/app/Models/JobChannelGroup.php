<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobChannelGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'job_id',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'whatsapp',
        'telegram',
        'email',
        'shared_url',
        'system',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
