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
        Schema::create('warehouse_product_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('item_type');
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            $table->integer('quantity'); // số lượng nhập/xuất
            $table->json('extras')->nullable();
            $table->enum('type', ['IMPORT', 'EXPORT']);
            $table->foreignId('warehouse_import_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_export_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_product_transactions');
    }
};
