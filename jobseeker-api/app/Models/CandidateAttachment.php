<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_link',
        'content_type',
        'user_attachments',
        'candidate_id',
        'created_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_attachments' => 'array',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
