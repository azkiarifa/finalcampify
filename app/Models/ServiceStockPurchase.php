<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceStockPurchase extends Model
{
    use HasFactory;

    protected $table = 'service_stock_purchases';

    protected $fillable = [
        'service_id',
        'invoice_number',
        'purchase_date',
        'received_at',
        'purchase_store',
        'vendor_phone',
        'vendor_address',
        'purchase_price',
        'quantity',
        'total_cost',
        'condition',
        'notes',
        'receipt_proof',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'received_at'    => 'datetime',
        'purchase_price' => 'integer',
        'quantity'       => 'integer',
        'total_cost'     => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->invoice_number)) {
                $purchase->invoice_number = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }
            if (empty($purchase->total_cost)) {
                $purchase->total_cost = (int) ($purchase->purchase_price ?? 0) * (int) ($purchase->quantity ?? 1);
            }
            if (empty($purchase->received_at)) {
                $purchase->received_at = now();
            }
        });
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function getReceiptUrlAttribute()
    {
        if (empty($this->receipt_proof)) {
            return null;
        }

        if (filter_var($this->receipt_proof, FILTER_VALIDATE_URL)) {
            return $this->receipt_proof;
        }

        if (str_starts_with($this->receipt_proof, 'uploads/') || str_starts_with($this->receipt_proof, 'assets/')) {
            return asset($this->receipt_proof);
        }

        return asset('uploads/vendor_receipts/' . $this->receipt_proof);
    }
}
