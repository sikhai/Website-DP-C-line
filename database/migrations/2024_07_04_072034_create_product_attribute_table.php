<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttributeTable extends Migration
{
    public function up()
    {
        Schema::create('product_attribute', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attribute_id');
            // Thêm các trường khác nếu cần

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
            $table->timestamps();

            // Tạo chỉ mục cho các cột nếu cần
            $table->index('product_id');
            $table->index('attribute_id');

            // Unique constraint nếu cần
            $table->unique(['product_id', 'attribute_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_attribute');
    }
}
