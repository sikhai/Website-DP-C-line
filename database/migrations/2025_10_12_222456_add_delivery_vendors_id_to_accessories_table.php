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
        Schema::table('accessories', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_vendors_id')->nullable()->after('category_id');
            $table->foreign('delivery_vendors_id')
                ->references('id')->on('delivery_vendors')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            $table->dropForeign(['delivery_vendors_id']);
            $table->dropColumn('delivery_vendors_id');
        });
    }
};
