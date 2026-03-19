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
        Schema::create('sample_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sample_request_id')
                ->constrained('sample_requests')
                ->cascadeOnDelete();

            $table->integer('quantity');

            $table->boolean('warehouse_confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_returns');
    }
};
