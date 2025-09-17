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
        Schema::table('tm_locations_new', function (Blueprint $table) {
            // Koordinat relatif terhadap gambar plant (untuk plant-level mapping)
            $table->decimal('plant_coordinate_x', 8, 6)->nullable()->after('coordinate_y');
            $table->decimal('plant_coordinate_y', 8, 6)->nullable()->after('plant_coordinate_x');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_locations_new', function (Blueprint $table) {
            $table->dropColumn(['plant_coordinate_x', 'plant_coordinate_y']);
        });
    }
};