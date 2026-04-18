<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_orders', function (Blueprint $table) {
            $table->string('status', 50)
                ->default('pending')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('sample_orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'ordered',
                'received'
            ])->default('pending')->change();
        });
    }
};