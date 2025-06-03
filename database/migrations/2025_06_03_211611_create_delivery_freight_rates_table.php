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
        Schema::create('delivery_freight_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('delivery_vendors')->onDelete('cascade');
            $table->foreignId('freight_type_id')->constrained('delivery_freight_types')->onDelete('cascade');
            $table->decimal('weight_from', 10, 2); // kg
            $table->decimal('weight_to', 10, 2)->nullable(); // null = no upper limit
            $table->decimal('rate', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_freight_rates');
    }
};
