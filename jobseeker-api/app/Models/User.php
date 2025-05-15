<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Constants\Roles;
use App\Traits\UseDeletedBy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, UseDeletedBy, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'phone',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'level',
    ];

    public function isSuperadmin(): bool
    {
        return $this->can('access admin dashboard');
    }

    public function isEmployer(): bool
    {
        return $this->can('access employer dashboard');
    }

    public function getJobPostedThisMonthCountAttribute(): int
    {
        if (!$this->isEmployer()) return 0;
        return $this->jobs()->whereMonth('created_at', now()->month)->count();
    }

    public function isPremium(): bool
    {
        return $this->package_type != 0;
    }

    public function getRoleName()
    {
        return Roles::getRoleName($this->role_id);
    }

    public function resume(): HasOne
    {
        return $this->hasOne(Resume::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscriptionPlans(): HasMany
    {
        return $this->hasMany(SubscriptionPlan::class);
    }

    public function providers()
    {
        return $this->hasMany(Provider::class, 'user_id', 'id');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function subscriptionTransactions(): HasMany
    {
        return $this->hasMany(SubscriptionTransaction::class, 'created_by', 'id');
    }

    public function unresolvedSubscriptionTransactions(): HasOne
    {
        return $this->HasOne(SubscriptionTransaction::class, 'created_by', 'id')->where('status', 'BELUM SELESAI');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function getPermissionAttribute()
    {
        return $this->getAllPermissions()->pluck('name');
    }

    public function subscriberJob(): HasOne
    {
        return $this->hasOne(SubscriberJob::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function subscriptionLevel(): int
    {
        if (!$this->isPremium()) return 0;

        $trx = $this->subscriptionTransactions()->where('status', 'SELESAI')->orderByDesc('ended_at')->first();
        if (!$trx) return 0;
        return $trx->subscriptionItem->level;
    }

    public function getLevelAttribute(): int
    {
        return $this->subscriptionLevel();
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(InterviewSchedule::class, 'created_by', 'id');
    }

    public function getInterviewPostedThisMonthCountAttribute(): int
    {
        if (!$this->isEmployer()) return 0;
        return $this->interviews()->whereMonth('created_at', now()->month)->count();
    }

    public function employerDetail(): HasOne
    {
        return $this->hasOne(EmployerDetail::class);
    }

    public function getEmployerPositionAttribute()
    {
        if ($this->employerDetail()->exists()) {
            return $this->employerDetail()->first()->position;
        }
        return null;
    }
}
