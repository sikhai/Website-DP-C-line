<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // USD, VND
            $table->string('name'); // Vietnamese Dong, US Dollar
            $table->string('symbol', 10)->nullable(); // $, ₫
            $table->decimal('exchange_rate', 15, 6)->default(1); // so với base currency
            $table->boolean('is_default')->default(false); // tiền mặc định
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
