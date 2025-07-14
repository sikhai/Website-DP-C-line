<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProjectsTable extends Migration
{
    /**
     * Thêm các cột mới vào bảng projects.
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('pic')->nullable();    
            $table->string('designer')->nullable();    
            $table->string('contractor')->nullable();  
            $table->unsignedBigInteger('owner_id')->nullable(); 
            $table->unsignedBigInteger('category_id')->nullable(); 

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Xóa các cột khi rollback.
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['category_id']);

            $table->dropColumn([
                'pic',
                'designer',
                'contractor',
                'owner_id',
                'category_id'
            ]);
        });
    }
}
