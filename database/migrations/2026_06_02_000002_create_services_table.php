<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->string('name');
                $table->enum('service_type', ['jual', 'sewa'])->default('sewa');
                $table->unsignedBigInteger('base_price')->default(0);
                $table->unsignedInteger('stock')->default(0);
                $table->unsignedInteger('min_duration_days')->default(1)->nullable();
                $table->unsignedBigInteger('escrow_deposit')->default(0);
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
