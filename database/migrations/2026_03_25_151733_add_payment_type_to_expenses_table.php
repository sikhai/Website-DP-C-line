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

            $table->enum('payment_type', [
                'company',
                'personal'
            ])->default('company')->after('status');

            $table->timestamp('reimbursed_at')->nullable()->after('paid_at');
            $table->foreignId('reimbursed_by')->nullable()->after('reimbursed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'reimbursed_at',
                'reimbursed_by'
            ]);
        });
    }
};
