<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('delivery_vendors', function (Blueprint $table) {
            $table->enum('type', ['Delivery', 'Accessories'])->default('Delivery')->after('point_of_origin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_vendors', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
