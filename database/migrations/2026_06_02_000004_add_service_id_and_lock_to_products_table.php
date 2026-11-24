<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'service_id')) {
                    $table->foreignId('service_id')->nullable()->after('store_id')->constrained('services')->nullOnDelete();
                }
                if (!Schema::hasColumn('products', 'is_locked')) {
                    $table->boolean('is_locked')->default(true)->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'service_id')) {
                    $table->dropForeign(['service_id']);
                    $table->dropColumn('service_id');
                }
                if (Schema::hasColumn('products', 'is_locked')) {
                    $table->dropColumn('is_locked');
                }
            });
        }
    }
};
