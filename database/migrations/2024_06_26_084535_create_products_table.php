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
        Schema::create('products', function (Blueprint $table) {
            $table->id('ProductID');
            $table->string('ProductName');
            $table->string('ProductCode')->unique();
            $table->text('Description')->nullable();
            $table->decimal('Price', 10, 2);
            $table->integer('StockQuantity');
            $table->string('ImageURL')->nullable();
            $table->text('Keywords')->nullable();
            $table->string('Status')->nullable();
            $table->boolean('IsFeatured')->default(false);
            $table->timestamps();  // This will add the CreatedAt and UpdatedAt columns with appropriate default values
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
