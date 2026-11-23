<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('nama_kategori')->nullable();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->timestamps();
            });

            // Seed default outdoor categories
            $defaults = [
                ['name' => 'Tenda', 'nama_kategori' => 'Tenda', 'slug' => 'tenda', 'description' => 'Tenda dome, tenda pleton, flysheet, dan aksesori perlindungan cuaca outdoor.'],
                ['name' => 'Tas Gunung (Carrier)', 'nama_kategori' => 'Tas Gunung (Carrier)', 'slug' => 'tas-gunung-carrier', 'description' => 'Carrier 35L - 80L, daypack, dan rain cover.'],
                ['name' => 'Alat Masak Camping', 'nama_kategori' => 'Alat Masak Camping', 'slug' => 'alat-masak-camping', 'description' => 'Kompor portable, nesting, gas kaleng, grill pan outdoor.'],
                ['name' => 'Sepatu & Sandal Gunung', 'nama_kategori' => 'Sepatu & Sandal Gunung', 'slug' => 'sepatu-sandal-gunung', 'description' => 'Sepatu trekking, sandal gunung anti-slip, dan gaiter.'],
                ['name' => 'Penerangan & Kelistrikan', 'nama_kategori' => 'Penerangan & Kelistrikan', 'slug' => 'penerangan-kelistrikan', 'description' => 'Headlamp, lentera tenda, solar panel outdoor.'],
                ['name' => 'Sleeping Bag & Matras', 'nama_kategori' => 'Sleeping Bag & Matras', 'slug' => 'sleeping-bag-matras', 'description' => 'Sleeping bag polar/bulu angsa, matras angin, hammock.'],
                ['name' => 'Aksesoris & Survival Kit', 'nama_kategori' => 'Aksesoris & Survival Kit', 'slug' => 'aksesoris-survival-kit', 'description' => 'Trekking pole, pisau lipat multifungsi, kompas, P3K outdoor.'],
            ];

            foreach ($defaults as $cat) {
                DB::table('categories')->insert(array_merge($cat, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
