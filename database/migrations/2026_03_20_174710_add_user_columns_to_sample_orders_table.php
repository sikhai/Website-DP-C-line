<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_orders', function (Blueprint $table) {

            $table->foreignId('ordered_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('received_by')
                ->nullable()
                ->after('ordered_by')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sample_orders', function (Blueprint $table) {
            $table->dropForeign(['ordered_by']);
            $table->dropForeign(['received_by']);

            $table->dropColumn(['ordered_by', 'received_by']);
        });
    }
};
