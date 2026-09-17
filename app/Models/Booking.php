<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [self::STATUS_PENDING, self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED, self::STATUS_CANCELLED];

    protected $fillable = ['client_id', 'service_id', 'technician_id', 'scheduled_at', 'address', 'latitude', 'longitude', 'description', 'status', 'started_at', 'completed_at'];

    protected $casts = ['scheduled_at' => 'datetime', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function media()
    {
        return $this->hasMany(BookingMedia::class);
    }

    public function report()
    {
        return $this->hasOne(JobReport::class);
    }
}
