<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestNote extends Model
{
    protected $fillable = [
        'service_request_id',
        'user_id',
        'technician_id',
        'body',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function authorName(): string
    {
        return $this->user->name ?? $this->technician->name ?? 'Unknown';
    }
}
