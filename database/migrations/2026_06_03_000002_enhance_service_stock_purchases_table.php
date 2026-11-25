<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('service_stock_purchases')) {
            Schema::table('service_stock_purchases', function (Blueprint $table) {
                if (!Schema::hasColumn('service_stock_purchases', 'invoice_number')) {
                    $table->string('invoice_number')->nullable()->after('service_id');
                }
                if (!Schema::hasColumn('service_stock_purchases', 'vendor_phone')) {
                    $table->string('vendor_phone')->nullable()->after('purchase_store');
                }
                if (!Schema::hasColumn('service_stock_purchases', 'vendor_address')) {
                    $table->text('vendor_address')->nullable()->after('vendor_phone');
                }
                if (!Schema::hasColumn('service_stock_purchases', 'total_cost')) {
                    $table->unsignedBigInteger('total_cost')->default(0)->after('purchase_price');
                }
                if (!Schema::hasColumn('service_stock_purchases', 'receipt_proof')) {
                    $table->string('receipt_proof')->nullable()->after('notes');
                }
                if (!Schema::hasColumn('service_stock_purchases', 'received_at')) {
                    $table->timestamp('received_at')->nullable()->after('purchase_date');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('service_stock_purchases')) {
            Schema::table('service_stock_purchases', function (Blueprint $table) {
                $columns = ['invoice_number', 'vendor_phone', 'vendor_address', 'total_cost', 'receipt_proof', 'received_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('service_stock_purchases', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
