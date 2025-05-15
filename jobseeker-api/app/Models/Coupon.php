<?php

namespace App\Models;

use App\Traits\UseCreatedBy;
use App\Traits\UseDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes, UseDeletedBy, UseCreatedBy;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'duration',
        'description',
        'expired_at',
        'value',
        'type',
    ];
}
