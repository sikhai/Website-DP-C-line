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
        Schema::table('designs', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->default(0.0)->after('slug');
            $table->decimal('currency_rate', 12, 2)->after('price')->default(0.0);
            $table->decimal('import_tax', 8, 2)->default(0.0)->after('currency_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->dropColumn(['price', 'currency_rate', 'import_tax']);
        });
    }
};
