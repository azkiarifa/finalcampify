<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SellerModels\Product_seller;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'store_id',
        'user_id',
        'category_id',
        'name',
        'service_type',
        'base_price',
        'stock',
        'min_duration_days',
        'escrow_deposit',
        'description',
        'image',
        'status',
    ];

    protected $casts = [
        'base_price' => 'integer',
        'stock' => 'integer',
        'min_duration_days' => 'integer',
        'escrow_deposit' => 'integer',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function details()
    {
        return $this->hasMany(ServiceDetail::class, 'service_id');
    }

    public function stockPurchases()
    {
        return $this->hasMany(ServiceStockPurchase::class, 'service_id')->latest('purchase_date');
    }

    public function latestPurchase()
    {
        return $this->hasOne(ServiceStockPurchase::class, 'service_id')->latestOfMany('purchase_date');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'service_id');
    }

    public function sellerProduct()
    {
        return $this->hasOne(Product_seller::class, 'service_id');
    }

    /**
     * Hitung total harga terkalkulasi (Base Price + Total Biaya Tambahan Kelengkapan)
     */
    public function calculateFinalPrice(): int
    {
        $additional = $this->details()
            ->where('is_included', true)
            ->sum('additional_price');

        return (int) ($this->base_price + $additional);
    }
}
