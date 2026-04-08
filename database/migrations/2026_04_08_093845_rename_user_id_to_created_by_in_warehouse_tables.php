<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * =========================
         * warehouse_exports
         * =========================
         */
        Schema::table('warehouse_exports', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_exports', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('user_id');
            }
        });

        DB::table('warehouse_exports')->update([
            'created_by' => DB::raw('user_id')
        ]);

        Schema::table('warehouse_exports', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        /**
         * =========================
         * warehouse_imports
         * =========================
         */
        Schema::table('warehouse_imports', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_imports', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('user_id');
            }
        });

        DB::table('warehouse_imports')->update([
            'created_by' => DB::raw('user_id')
        ]);

        Schema::table('warehouse_imports', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        /**
         * =========================
         * warehouse_product_transactions
         * =========================
         */
        Schema::table('warehouse_product_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_product_transactions', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('user_id');
            }
        });

        DB::table('warehouse_product_transactions')->update([
            'created_by' => DB::raw('user_id')
        ]);

        Schema::table('warehouse_product_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        /**
         * =========================
         * warehouse_exports
         * =========================
         */
        Schema::table('warehouse_exports', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_exports', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('created_by');
            }
        });

        DB::table('warehouse_exports')->update([
            'user_id' => DB::raw('created_by')
        ]);

        Schema::table('warehouse_exports', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        /**
         * =========================
         * warehouse_imports
         * =========================
         */
        Schema::table('warehouse_imports', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_imports', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('created_by');
            }
        });

        DB::table('warehouse_imports')->update([
            'user_id' => DB::raw('created_by')
        ]);

        Schema::table('warehouse_imports', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        /**
         * =========================
         * warehouse_product_transactions
         * =========================
         */
        Schema::table('warehouse_product_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_product_transactions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('created_by');
            }
        });

        DB::table('warehouse_product_transactions')->update([
            'user_id' => DB::raw('created_by')
        ]);

        Schema::table('warehouse_product_transactions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};