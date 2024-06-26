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
            $table->unsignedBigInteger('ProductID');
            $table->unsignedBigInteger('AttributeID');
            $table->text('Value')->nullable();
            $table->timestamps();  // This will add the CreatedAt and UpdatedAt columns with appropriate default values

            $table->primary(['ProductID', 'AttributeID']);
            $table->foreign('ProductID')->references('ProductID')->on('products')->onDelete('cascade');
            $table->foreign('AttributeID')->references('AttributeID')->on('attributes')->onDelete('cascade');
            
            // Add index with length for TEXT column
            $table->index(['AttributeID', DB::raw('Value(255)')], 'idx_product_attribute');
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

