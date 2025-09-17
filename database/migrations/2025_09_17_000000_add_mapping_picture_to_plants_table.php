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
        Schema::table('tm_plants', function (Blueprint $table) {
            $table->string('plant_mapping_picture')->nullable()->after('plant_name');
            $table->text('plant_description')->nullable()->after('plant_mapping_picture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_plants', function (Blueprint $table) {
            $table->dropColumn(['plant_mapping_picture', 'plant_description']);
        });
    }
};