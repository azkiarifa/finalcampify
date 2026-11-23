<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceDetail extends Model
{
    use HasFactory;

    protected $table = 'service_details';

    protected $fillable = [
        'service_id',
        'item_name',
        'specification',
        'additional_price',
        'is_included',
    ];

    protected $casts = [
        'additional_price' => 'integer',
        'is_included' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
