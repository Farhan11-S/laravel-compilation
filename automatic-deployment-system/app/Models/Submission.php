<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'folder_path',
        'submitted_by',
        'assignment_id',
        'file_list',
    ];

    protected $casts = [
        'file_list' => 'array',
    ];

    public function assignments_criterias() {
        return $this->hasManyThrough(AssignmentCriteria::class, Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function submitted_by()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
}
