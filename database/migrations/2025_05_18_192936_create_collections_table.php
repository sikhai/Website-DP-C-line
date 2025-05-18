<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('shape')->nullable();
            $table->string('image')->nullable(); // Đường dẫn ảnh chính
            $table->json('images')->nullable();  // Nhiều ảnh (dưới dạng JSON)
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable(); // ID của category cha
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
