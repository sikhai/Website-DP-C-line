<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('warehouse_export_product', function (Blueprint $table) {
            $table->decimal('stock_quantity', 10, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_export_product', function (Blueprint $table) {
            $table->integer('stock_quantity')->change();
        });
    }
};
