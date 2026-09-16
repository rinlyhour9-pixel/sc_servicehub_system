<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
        self::PRIORITY_URGENT,
    ];

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'service_category_id',
        'assigned_technician_id',
        'created_by',
        'title',
        'description',
        'service_address',
        'status',
        'priority',
        'scheduled_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ServiceRequest $serviceRequest) {
            if (empty($serviceRequest->ticket_number)) {
                $serviceRequest->ticket_number = 'SR-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class, 'assigned_technician_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notes()
    {
        return $this->hasMany(ServiceRequestNote::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(ServiceRequestAttachment::class)->latest();
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function statusLabel(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_ASSIGNED => 'bg-blue-100 text-blue-800',
            self::STATUS_IN_PROGRESS => 'bg-indigo-100 text-indigo-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function priorityBadgeColor(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'bg-gray-100 text-gray-700',
            self::PRIORITY_MEDIUM => 'bg-blue-100 text-blue-700',
            self::PRIORITY_HIGH => 'bg-orange-100 text-orange-700',
            self::PRIORITY_URGENT => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
