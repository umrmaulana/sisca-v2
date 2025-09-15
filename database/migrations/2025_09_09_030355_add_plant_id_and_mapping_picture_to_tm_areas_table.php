<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tm_areas', function (Blueprint $table) {
            $table->unsignedBigInteger('plant_id')->nullable()->after('area_name');
            $table->string('mapping_picture')->nullable()->after('plant_id');

            $table->foreign('plant_id')->references('id')->on('tm_plants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_areas', function (Blueprint $table) {
            $table->dropForeign(['plant_id']);
            $table->dropColumn(['plant_id', 'mapping_picture']);
        });
    }
};
