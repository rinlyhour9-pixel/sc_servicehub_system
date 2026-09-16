<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestAttachment extends Model
{
    protected $fillable = [
        'service_request_id',
        'uploaded_by',
        'file_path',
        'original_name',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->file_path);
    }
}
