<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('warehouse_imports', function (Blueprint $table) {
            $table->string('status')->default('DRAFT')->after('project_id');

            $table->foreignId('confirmed_by')->nullable()->after('status');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
        });

        Schema::table('warehouse_exports', function (Blueprint $table) {
            $table->string('status')->default('DRAFT')->after('project_id');

            $table->foreignId('confirmed_by')->nullable()->after('status');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_imports', function (Blueprint $table) {
            $table->dropColumn(['status', 'confirmed_by', 'confirmed_at']);
        });

        Schema::table('warehouse_exports', function (Blueprint $table) {
            $table->dropColumn(['status', 'confirmed_by', 'confirmed_at']);
        });
    }
};
