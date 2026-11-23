<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('service_stock_purchases')) {
            Schema::create('service_stock_purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->date('purchase_date'); // Tanggal Beli
                $table->string('purchase_store'); // Beli di mana (Toko / Distributor / Vendor)
                $table->unsignedBigInteger('purchase_price')->default(0); // Harga modal beli per unit
                $table->unsignedInteger('quantity')->default(1); // Jumlah unit masuk
                $table->string('condition')->default('baru'); // baru, bekas_mulus, rekondisi
                $table->text('notes')->nullable(); // Catatan nota / nomor seri alat
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_stock_purchases');
    }
};
