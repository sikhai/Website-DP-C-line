<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseImportsTable extends Migration
{
    public function up()
    {
        Schema::create('warehouse_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');      // ID sản phẩm
            $table->decimal('weight', 10, 2);              // Khối lượng nhập (hỗ trợ số thập phân)
            $table->string('import_code')->unique();       // Mã code nhập (duy nhất)
            $table->unsignedBigInteger('user_id');         // Người thực hiện nhập kho
            $table->unsignedBigInteger('project_id')->nullable(); // Dự án liên quan (có thể null)
            $table->json('more_info')->nullable();         // Trường json bổ sung
            $table->timestamps();

            // Foreign key nếu cần
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_imports');
    }
}
