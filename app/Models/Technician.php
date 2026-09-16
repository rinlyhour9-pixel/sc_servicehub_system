<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Technician extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function assignedServiceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_technician_id');
    }

    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_category_technician');
    }
}
