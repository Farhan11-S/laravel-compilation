<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeCriteria extends Model
{
    protected $fillable = [
        "grade_id",
        "assignment_criteria_id"
    ];

    public function assignment_criteria(){
        return $this->belongsTo(AssignmentCriteria::class);
    }

    public function grade(){
        return $this->belongsTo(Grade::class);
    }
}
