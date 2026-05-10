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
        Schema::create('business_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('destination')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('estimated_amount', 15, 2)->default(0);
            $table->decimal('total_advanced_amount', 15, 2)->default(0);
            $table->decimal('actual_spent_amount', 15, 2)->default(0);
            $table->decimal('difference_amount', 15, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('employee_confirmed_at')->nullable();
            $table->timestamp('accountant_confirmed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_trips');
    }
};
