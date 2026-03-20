<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {

            // đổi sang unsignedBigInteger
            $table->unsignedBigInteger('requested_by')->nullable()->change();
            $table->unsignedBigInteger('approved_by')->nullable()->change();

            // index
            $table->index('requested_by');
            $table->index('approved_by');

            // foreign key
            $table->foreign('requested_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {

            // drop foreign key trước
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['approved_by']);

            // drop index
            $table->dropIndex(['requested_by']);
            $table->dropIndex(['approved_by']);

            // (optional) đổi lại kiểu cũ nếu cần
            // $table->string('requested_by')->nullable()->change();
            // $table->string('approved_by')->nullable()->change();
        });
    }
};