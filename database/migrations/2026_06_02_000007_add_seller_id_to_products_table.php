<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'seller_id')) {
                    $table->foreignId('seller_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
                }
            });

            // Backfill seller_id with user_id
            DB::statement('UPDATE products SET seller_id = user_id WHERE seller_id IS NULL AND user_id IS NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'seller_id')) {
                    $table->dropForeign(['seller_id']);
                    $table->dropColumn('seller_id');
                }
            });
        }
    }
};
