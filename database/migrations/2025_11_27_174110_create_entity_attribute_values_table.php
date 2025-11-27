<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityAttributeValuesTable extends Migration
{
    public function up()
    {
        Schema::create('entity_attribute_values', function (Blueprint $table) {
            $table->bigIncrements('id');

            // polymorphic keys
            $table->string('entity_type')->index(); // e.g. App\Models\Design
            $table->unsignedBigInteger('entity_id')->index();

            // point to attributes & attribute_values
            $table->unsignedBigInteger('attribute_id')->index();
            $table->unsignedBigInteger('attribute_value_id')->index();

            $table->timestamps();

            // Optional: enforce logical uniqueness to avoid duplicate assignment
            $table->unique(['entity_type', 'entity_id', 'attribute_id', 'attribute_value_id'], 'entity_attr_value_unique');
        });

        // Additional composite index for common filter patterns
        Schema::table('entity_attribute_values', function (Blueprint $table) {
            $table->index(['entity_type', 'attribute_id', 'attribute_value_id'], 'idx_entity_attr_val');
        });
    }

    public function down()
    {
        Schema::dropIfExists('entity_attribute_values');
    }
}

