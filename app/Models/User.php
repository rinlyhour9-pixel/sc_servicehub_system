<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'role',
        'availability',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'availability' => 'array',
    ];

    public const ROLE_CLIENT = 'client';

    public const ROLE_TECHNICIAN = 'technician';

    public const ROLE_ADMIN = 'admin';

    public function createdServiceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'created_by');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function assignedBookings()
    {
        return $this->hasMany(Booking::class, 'technician_id');
    }
}
