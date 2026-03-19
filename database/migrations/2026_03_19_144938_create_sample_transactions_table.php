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
        Schema::create('sample_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sample_id')
                ->constrained('samples')
                ->cascadeOnDelete();

            $table->enum('type', ['in', 'out']);

            $table->integer('quantity');

            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');

            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_transactions');
    }
};
