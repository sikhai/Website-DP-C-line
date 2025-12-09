<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Xóa bảng chi tiết trước để tránh lỗi foreign key
        Schema::dropIfExists('warehouse_import_product');
        Schema::dropIfExists('warehouse_export_product');

        Schema::dropIfExists('warehouse_imports');
        Schema::dropIfExists('warehouse_exports');
    }

    public function down(): void
    {
        // rollback không cần tạo lại vì bạn đang chuẩn bị tạo hệ thống mới
    }
};
