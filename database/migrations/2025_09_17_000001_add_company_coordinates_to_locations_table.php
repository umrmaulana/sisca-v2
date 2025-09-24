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
            // Koordinat relatif terhadap gambar company (untuk company-level mapping)
            $table->decimal('company_coordinate_x', 8, 6)->nullable()->after('coordinate_y');
            $table->decimal('company_coordinate_y', 8, 6)->nullable()->after('company_coordinate_x');
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
            $table->dropColumn(['company_coordinate_x', 'company_coordinate_y']);
        });
    }
};