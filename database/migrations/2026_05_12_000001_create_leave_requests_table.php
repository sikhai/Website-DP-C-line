<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days_count', 5, 2)->default(0);
            $table->string('type', 50);
            $table->string('status', 50)->default('pending');
            $table->text('reason')->nullable();
            $table->text('manager_note')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_disk', 50)->default('public');
            $table->string('attachment_original_name')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'start_date', 'end_date']);
            $table->index(['approved_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
