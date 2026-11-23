<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_ratings')) {
            Schema::table('product_ratings', function (Blueprint $table) {
                if (!Schema::hasColumn('product_ratings', 'replied_at')) {
                    $table->timestamp('replied_at')->nullable()->after('reply');
                }
            });
        }

        if (Schema::hasTable('store_ratings')) {
            Schema::table('store_ratings', function (Blueprint $table) {
                if (!Schema::hasColumn('store_ratings', 'replied_at')) {
                    $table->timestamp('replied_at')->nullable()->after('reply');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_ratings')) {
            Schema::table('product_ratings', function (Blueprint $table) {
                if (Schema::hasColumn('product_ratings', 'replied_at')) {
                    $table->dropColumn('replied_at');
                }
            });
        }

        if (Schema::hasTable('store_ratings')) {
            Schema::table('store_ratings', function (Blueprint $table) {
                if (Schema::hasColumn('store_ratings', 'replied_at')) {
                    $table->dropColumn('replied_at');
                }
            });
        }
    }
};
