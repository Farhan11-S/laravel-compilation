<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{

    protected $table = 'grades';

    protected $fillable = [
        "grade",
        "submission_id",
        "created_by",
        "note",
        "aesthetic_score",
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function criterias()
    {
        return $this->hasMany(GradeCriteria::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
