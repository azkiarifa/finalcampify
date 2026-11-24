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
        });
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
