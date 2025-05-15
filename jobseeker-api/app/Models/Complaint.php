<?php

namespace App\Models;

use App\Traits\UseDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use HasFactory, SoftDeletes, UseDeletedBy;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'category',
        'department',
        'created_by',
        'deleted_by',
    ];

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
