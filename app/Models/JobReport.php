<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobReport extends Model
{
    protected $fillable = ['booking_id', 'technician_id', 'report', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
