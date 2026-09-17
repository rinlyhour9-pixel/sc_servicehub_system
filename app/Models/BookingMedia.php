<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingMedia extends Model
{
    protected $fillable = ['booking_id', 'uploaded_by', 'type', 'path', 'original_name', 'mime_type'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
