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
        Schema::create('sample_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sample_id')
                ->constrained('samples')
                ->cascadeOnDelete();

            $table->integer('quantity');

            $table->string('requested_by');
            $table->string('used_for')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'picked_up',
                'returned',
                'rejected'
            ])->default('pending');

            $table->timestamps();

            $table->index(['sample_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_requests');
    }
};
