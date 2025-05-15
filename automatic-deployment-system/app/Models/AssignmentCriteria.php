<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentCriteria extends Model
{
    protected $fillable = ['assignment_id','criteria'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
