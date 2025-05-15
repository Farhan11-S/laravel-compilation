<?php

namespace App\Models;

use App\Traits\UseDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes, UseDeletedBy;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'can_access_list' => 'boolean',
        'can_access_detail' => 'boolean',
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'slug',
        'link',
        'parent',
        'place',
        'can_access_list',
        'can_access_detail',
        'can_create',
        'can_update',
        'can_delete',
        'position',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent', 'id');
    }
}
