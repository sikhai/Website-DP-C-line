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
        Schema::table('sample_returns', function (Blueprint $table) {
            if (Schema::hasColumn('sample_returns', 'warehouse_confirmed')) {
                $table->dropColumn('warehouse_confirmed');
            }

            $table->foreignId('confirmed_by')
                ->nullable()
                ->after('quantity')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sample_returns', function (Blueprint $table) {
            $table->boolean('warehouse_confirmed')
                ->default(false)
                ->after('quantity');

            $table->dropForeign(['confirmed_by']);
            $table->dropColumn('confirmed_by');
        });
    }
};
