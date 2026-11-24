<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'label',
        'receiver_name',
        'phone',
        'alamat',
        'kota',
        'kecamatan',
        'kode_pos',
        'is_primary',
        'address',
        'city',
        'district',
        'postal_code',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['alamat'] = $value;
    }

    public function getAddressAttribute()
    {
        return $this->attributes['alamat'] ?? '';
    }

    public function setCityAttribute($value)
    {
        $this->attributes['kota'] = $value;
    }

    public function getCityAttribute()
    {
        return $this->attributes['kota'] ?? '';
    }

    public function setDistrictAttribute($value)
    {
        $this->attributes['kecamatan'] = $value;
    }

    public function getDistrictAttribute()
    {
        return $this->attributes['kecamatan'] ?? '';
    }

    public function setPostalCodeAttribute($value)
    {
        $this->attributes['kode_pos'] = $value;
    }

    public function getPostalCodeAttribute()
    {
        return $this->attributes['kode_pos'] ?? '';
    }
}
