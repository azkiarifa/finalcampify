<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah receiver_name di users jika belum ada
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'receiver_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('receiver_name')->nullable()->after('name');
            });
        }

        // 2. Buat tabel user_addresses jika belum ada
        if (!Schema::hasTable('user_addresses')) {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('label')->default('Alamat Rumah');
                $table->string('receiver_name');
                $table->string('phone');
                $table->text('address');
                $table->string('city');
                $table->string('district')->nullable();
                $table->string('postal_code')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'receiver_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('receiver_name');
            });
        }
    }
};
