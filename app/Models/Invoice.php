<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_UNPAID,
        self::STATUS_PAID,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'service_request_id',
        'invoice_number',
        'status',
        'issued_at',
        'paid_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
            }
            if (empty($invoice->issued_at)) {
                $invoice->issued_at = now();
            }
        });
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function walletTransaction()
    {
        return $this->hasOne(WalletTransaction::class);
    }

    public function total(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->quantity * $item->unit_price);
    }
}
