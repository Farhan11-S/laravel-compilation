<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Resume extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
    ];

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function language_skills(): HasMany
    {
        return $this->hasMany(LanguageSkill::class);
    }

    public function user_detail(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    public function work_experiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
