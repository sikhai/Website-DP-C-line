<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_import_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_import_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->integer('stock_quantity'); // số lượng nhập kho cho sản phẩm

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_import_product');
    }
};