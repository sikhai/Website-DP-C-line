<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->decimal('discount_price', 12, 2)
                  ->nullable()
                  ->after('price');

            $table->unsignedInteger('discount_quantity')
                  ->nullable()
                  ->after('discount_price');
        });
    }

    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->dropColumn([
                'discount_price',
                'discount_quantity'
            ]);
        });
    }
};