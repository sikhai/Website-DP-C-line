<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();

            $table->string('image')->nullable();

            $table->string('borrow_status', 50)->default('borrowing');
        });
    }

    public function down(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['assigned_to']);

            $table->dropColumn([
                'client_id',
                'unit_id',
                'project_id',
                'assigned_to',
                'expected_return_date',
                'actual_return_date',
                'image',
                'borrow_status',
            ]);
        });
    }
};