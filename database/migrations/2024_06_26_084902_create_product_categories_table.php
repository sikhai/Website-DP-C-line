<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('ProductID');
            $table->unsignedBigInteger('CategoryID');
            $table->timestamps();  // This will add the CreatedAt and UpdatedAt columns with appropriate default values

            $table->primary(['ProductID', 'CategoryID']);
            $table->foreign('ProductID')->references('ProductID')->on('products')->onDelete('cascade');
            $table->foreign('CategoryID')->references('CategoryID')->on('categories')->onDelete('cascade');
            $table->index(['ProductID', 'CategoryID'], 'idx_product_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
