<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mustahik extends Model
{
    protected $table = 'mustahik';

    protected $fillable = [
        'uuid',
        'jemaah_id',
        'verified_by',
        'is_disabled',
    ];
}
