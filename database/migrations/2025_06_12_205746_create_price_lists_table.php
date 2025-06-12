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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã định danh cho mỗi lần export
            $table->text('quote_data'); // Dữ liệu báo giá đã xuất (có thể lưu JSON)
            $table->unsignedBigInteger('user_id'); // ID người dùng đã export
            $table->timestamps();

            // Ràng buộc khóa ngoại (nếu có bảng users)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
