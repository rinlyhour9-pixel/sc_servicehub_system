<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyOperation extends Model
{
    protected $fillable = [
        'business_date', 'opened_by', 'opened_at', 'opening_notes', 'opening_cash',
        'closed_by', 'closed_at', 'closing_notes', 'expected_cash', 'actual_cash', 'cash_difference',
    ];

    protected $casts = ['business_date' => 'date', 'opened_at' => 'datetime', 'closed_at' => 'datetime'];

    public function opener() { return $this->belongsTo(User::class, 'opened_by'); }
    public function closer() { return $this->belongsTo(User::class, 'closed_by'); }
    public function walletTransactions() { return $this->hasMany(WalletTransaction::class); }
    public function isOpen(): bool { return is_null($this->closed_at); }
}
