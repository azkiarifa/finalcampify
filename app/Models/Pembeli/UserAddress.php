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
        'address',
        'city',
        'district',
        'postal_code',
        'is_primary',
        // Compatibility aliases
        'alamat',
        'kota',
        'kecamatan',
        'kode_pos',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Mutator & Accessor for Indonesian alias 'alamat' -> 'address'
    public function setAlamatAttribute($value)
    {
        $this->attributes['address'] = $value;
    }

    public function getAlamatAttribute()
    {
        return $this->attributes['address'] ?? '';
    }

    // Mutator & Accessor for Indonesian alias 'kota' -> 'city'
    public function setKotaAttribute($value)
    {
        $this->attributes['city'] = $value;
    }

    public function getKotaAttribute()
    {
        return $this->attributes['city'] ?? '';
    }

    // Mutator & Accessor for Indonesian alias 'kecamatan' -> 'district'
    public function setKecamatanAttribute($value)
    {
        $this->attributes['district'] = $value;
    }

    public function getKecamatanAttribute()
    {
        return $this->attributes['district'] ?? '';
    }

    // Mutator & Accessor for Indonesian alias 'kode_pos' -> 'postal_code'
    public function setKodePosAttribute($value)
    {
        $this->attributes['postal_code'] = $value;
    }

    public function getKodePosAttribute()
    {
        return $this->attributes['postal_code'] ?? '';
    }
}
