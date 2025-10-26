<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kiểm tra nếu chưa có cột thì mới thêm để tránh lỗi khi migrate nhiều lần
        if (!Schema::hasColumn('price_lists', 'clients_id')) {
            Schema::table('price_lists', function (Blueprint $table) {
                // ✅ Thêm cột clients_id tự động sinh từ JSON
                $table->unsignedBigInteger('clients_id')
                    ->storedAs("JSON_UNQUOTE(JSON_EXTRACT(quote_data, '$.clients_id'))")
                    ->nullable()
                    ->after('quote_data')
                    ->index();

                // ✅ Nếu bảng clients tồn tại thì mới tạo foreign key
                if (Schema::hasTable('clients')) {
                    $table->foreign('clients_id')
                        ->references('id')
                        ->on('clients')
                        ->onDelete('set null'); // hoặc ->cascadeOnDelete() tùy logic
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('price_lists', function (Blueprint $table) {
            // Nếu có foreign key thì xóa trước
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('price_lists');

            if ($doctrineTable->hasForeignKey('price_lists_clients_id_foreign')) {
                $table->dropForeign('price_lists_clients_id_foreign');
            }

            if (Schema::hasColumn('price_lists', 'clients_id')) {
                $table->dropColumn('clients_id');
            }
        });
    }
};
