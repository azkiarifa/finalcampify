<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'nama_kategori',
        'slug',
        'description',
        'icon',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->name) && !empty($category->nama_kategori)) {
                $category->name = $category->nama_kategori;
            } elseif (empty($category->nama_kategori) && !empty($category->name)) {
                $category->nama_kategori = $category->name;
            }

            if (empty($category->slug) && !empty($category->name)) {
                $category->slug = Str::slug($category->name);
            }

            if (empty($category->icon)) {
                $category->icon = static::guessIcon($category->name);
            }
        });
    }

    public static function guessIcon(?string $name): string
    {
        $name = strtolower($name ?? '');
        if (str_contains($name, 'tenda') || str_contains($name, 'shelter')) return 'bi-house-door';
        if (str_contains($name, 'masak') || str_contains($name, 'kompor') || str_contains($name, 'kitchen') || str_contains($name, 'cooking')) return 'bi-fire';
        if (str_contains($name, 'sepatu') || str_contains($name, 'sandal') || str_contains($name, 'footwear')) return 'bi-boot';
        if (str_contains($name, 'tas') || str_contains($name, 'carrier') || str_contains($name, 'backpack') || str_contains($name, 'ransel')) return 'bi-backpack';
        if (str_contains($name, 'tidur') || str_contains($name, 'sleeping') || str_contains($name, 'matras')) return 'bi-moon-stars';
        if (str_contains($name, 'lampu') || str_contains($name, 'senter') || str_contains($name, 'penerangan')) return 'bi-lightbulb';
        if (str_contains($name, 'pakaian') || str_contains($name, 'jaket') || str_contains($name, 'baju')) return 'bi-person';
        if (str_contains($name, 'navigasi') || str_contains($name, 'kompas') || str_contains($name, 'gps')) return 'bi-compass';
        if (str_contains($name, 'survival') || str_contains($name, 'safety') || str_contains($name, 'medis')) return 'bi-shield-plus';
        return 'bi-tag';
    }

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['nama_kategori'] ?? null;
    }

    public function getNamaKategoriAttribute()
    {
        return $this->attributes['nama_kategori'] ?? $this->attributes['name'] ?? null;
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'kategori_id');
    }
}
