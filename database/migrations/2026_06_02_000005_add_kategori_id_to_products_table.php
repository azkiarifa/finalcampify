<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'kategori_id')) {
                    $table->foreignId('kategori_id')->nullable()->constrained('categories')->nullOnDelete();
                }
                if (!Schema::hasColumn('products', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'kategori_id')) {
                    $table->dropForeign(['kategori_id']);
                    $table->dropColumn('kategori_id');
                }
                if (Schema::hasColumn('products', 'category_id')) {
                    $table->dropForeign(['category_id']);
                    $table->dropColumn('category_id');
                }
            });
        }
    }
};
