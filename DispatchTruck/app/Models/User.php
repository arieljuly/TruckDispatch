<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'email',
        'password',
        'role_id',
        'status',
        'email_verified_at',
        'company_name',
        'address',
        'city',
        'state',
        'postal_code',
        'preferred_contact_method',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    /**
     * Get user's initials.
     */
    public function initials(): string
    {
        return Str::of($this->first_name)
            ->explode(' ')
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Role check methods.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isDispatcher(): bool
    {
        return $this->hasRole('dispatcher');
    }

    public function isDriver(): bool
    {
        return $this->hasRole('driver');
    }

    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    /**
     * Client-specific methods.
     */
    public function canCreateDeliveryRequest(): bool
    {
        return $this->isClient() || $this->isDispatcher() || $this->isAdmin();
    }

    public function getClientFullAddress(): string
    {
        return trim($this->address . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postal_code);
    }

    /**
     * Scopes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, string $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('role_name', $roleName);
        });
    }

    public function scopeClients($query)
    {
        return $query->byRole('client');
    }

    public function scopeDrivers($query)
    {
        return $query->byRole('driver');
    }

    public function scopeDispatchers($query)
    {
        return $query->byRole('dispatcher');
    }

    public function scopeAdmins($query)
    {
        return $query->byRole('admin');
    }

    /**
     * Relationships.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function deliveryRequests()
    {
        return $this->hasMany(DeliveryRequest::class, 'requested_by');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(Maintenance::class, 'reported_by');
    }

    public function completedMaintenance()
    {
        return $this->hasMany(Maintenance::class, 'completed_by');
    }

    public function dispatchSessions()
    {
        return $this->hasMany(DispatchSession::class, 'executed_by');
    }

    public function truckAssignments()
    {
        return $this->hasMany(TruckAssignment::class, 'assigned_by');
    }
}