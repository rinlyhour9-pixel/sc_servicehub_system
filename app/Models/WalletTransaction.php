<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WalletTransaction extends Model {
    public const TYPES=['sale','expense','receivable','refund','delivery_payment'];
    public const METHODS=['cash','bank_qr','other'];
    protected $fillable=['daily_operation_id','created_by','invoice_id','type','payment_method','amount','description'];
    protected $casts=['amount'=>'decimal:2'];
    public function dailyOperation(){return $this->belongsTo(DailyOperation::class);}
    public function invoice(){return $this->belongsTo(Invoice::class);}
}
