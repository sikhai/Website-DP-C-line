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
        Schema::create('sample_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sample_id')
                ->constrained('samples')
                ->cascadeOnDelete();

            $table->integer('quantity');

            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();

            $table->enum('status', [
                'pending',
                'ordered',
                'received'
            ])->default('pending');

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['sample_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_orders');
    }
};
