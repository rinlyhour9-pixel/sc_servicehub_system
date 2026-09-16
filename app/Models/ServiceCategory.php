<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function technicians()
    {
        return $this->belongsToMany(Technician::class, 'service_category_technician');
    }
}
