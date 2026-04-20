<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_returns', function (Blueprint $table) {
            $table->foreignId('returned_by')
                ->nullable()
                ->after('note')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sample_returns', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropColumn('returned_by');
        });
    }
};