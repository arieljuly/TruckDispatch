<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role_name',
        'description',
    ];

    /**
     * Get the users that belong to the role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role_name === 'admin';
    }

    /**
     * Check if role is dispatcher.
     */
    public function isDispatcher(): bool
    {
        return $this->role_name === 'dispatcher';
    }

    /**
     * Check if role is driver.
     */
    public function isDriver(): bool
    {
        return $this->role_name === 'driver';
    }
    public function isClient():bool
    {
        return $this->role_name === 'client';
    }   
}