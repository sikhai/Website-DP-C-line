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
        Schema::create('cocqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exporter_id')->nullable(); // liên kết user
            $table->timestamp('export_time')->nullable();
            $table->string('price_list_code');
            $table->timestamps();

            // Index & foreign key
            $table->index('price_list_code');
            $table->foreign('exporter_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cocqs');
    }
};
