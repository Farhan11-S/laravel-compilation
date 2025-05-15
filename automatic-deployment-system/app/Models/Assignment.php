<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['title', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function criterias()
    {
        return $this->hasMany(AssignmentCriteria::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
