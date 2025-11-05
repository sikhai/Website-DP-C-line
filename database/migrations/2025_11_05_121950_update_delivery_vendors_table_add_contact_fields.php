<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_vendors', function (Blueprint $table) {
            // name hiện là mã định danh → đảm bảo không trùng
            $table->unique('name');

            // Thêm các trường mới
            $table->string('vendor_name')->nullable()->after('name'); // tên hiển thị
            $table->string('tax_id')->nullable()->after('vendor_name');
            $table->string('address')->nullable()->after('tax_id');
            $table->string('email')->nullable()->after('address');
            $table->string('phone')->nullable()->after('email');
            $table->string('fax')->nullable()->after('phone');
            $table->string('contact_name')->nullable()->after('fax');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_vendors', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropColumn([
                'vendor_name',
                'tax_id',
                'address',
                'email',
                'phone',
                'fax',
                'contact_name',
            ]);
        });
    }
};
