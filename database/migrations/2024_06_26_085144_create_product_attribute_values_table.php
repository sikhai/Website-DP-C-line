<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attribute_id');
            $table->text('value')->nullable();
            $table->timestamps();  // This will add the CreatedAt and UpdatedAt columns with appropriate default values

            $table->primary(['product_id', 'attribute_id']);
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('attributes')->onDelete('cascade');
            
            // Add index with length for TEXT column
            $table->index(['attribute_id', DB::raw('Value(255)')], 'idx_product_attribute');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_attribute_values');
    }
}

