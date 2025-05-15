<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'level',
        'field_of_study',
        'school_name',
        'country',
        'city',
        'is_currently_enrolled',
        'from',
        'to',
        'resume_id',
    ];

    protected $table = 'educations';

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
