<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStockPurchase extends Model
{
    use HasFactory;

    protected $table = 'service_stock_purchases';

    protected $fillable = [
        'service_id',
        'purchase_date',
        'purchase_store',
        'purchase_price',
        'quantity',
        'condition',
        'notes',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'purchase_price' => 'integer',
        'quantity'       => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
