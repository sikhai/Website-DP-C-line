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
        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('has_vat')->default(false)->after('amount');

            $table->decimal('vat_rate', 5, 2)->default(0)->after('has_vat');
            // ví dụ: 11.00 (%)

            $table->decimal('vat_amount', 15, 2)->default(0)->after('vat_rate');

            $table->decimal('total_amount', 15, 2)->default(0)->after('vat_amount');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'has_vat',
                'vat_rate',
                'vat_amount',
                'total_amount'
            ]);
        });
    }
};
