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
        Schema::create('delivery_freight_types', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., air_freight, ocean_freight
            $table->string('name'); // e.g., "Air Freight", "Ocean Freight"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_freight_types');
    }
};
