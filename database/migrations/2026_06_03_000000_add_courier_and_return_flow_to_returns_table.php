<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            if (!Schema::hasColumn('returns', 'kurir_return')) {
                $table->string('kurir_return')->nullable();
            }
            if (!Schema::hasColumn('returns', 'booking_code_return')) {
                $table->string('booking_code_return')->nullable();
            }
            if (!Schema::hasColumn('returns', 'kurir_pengganti')) {
                $table->string('kurir_pengganti')->nullable();
            }
            if (!Schema::hasColumn('returns', 'booking_code_pengganti')) {
                $table->string('booking_code_pengganti')->nullable();
            }
            if (!Schema::hasColumn('returns', 'ongkir_bearer')) {
                $table->string('ongkir_bearer')->nullable();
            }
            if (!Schema::hasColumn('returns', 'stock_replacement_empty')) {
                $table->boolean('stock_replacement_empty')->default(false);
            }
            if (!Schema::hasColumn('returns', 'offer_refund_alternative')) {
                $table->boolean('offer_refund_alternative')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $columnsToDrop = [
                'kurir_return',
                'booking_code_return',
                'kurir_pengganti',
                'booking_code_pengganti',
                'ongkir_bearer',
                'stock_replacement_empty',
                'offer_refund_alternative',
            ];
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('returns', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
