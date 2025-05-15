<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_name',
        'color_hex',
        'job_id',
        'started_at',
        'ended_at',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
