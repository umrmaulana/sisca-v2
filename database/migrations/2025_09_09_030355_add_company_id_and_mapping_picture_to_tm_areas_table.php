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
            $table->unsignedBigInteger('company_id')->nullable()->after('area_name');
            $table->string('mapping_picture')->nullable()->after('company_id');

            $table->foreign('company_id')->references('id')->on('tm_companies')->onDelete('cascade');
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
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'mapping_picture']);
        });
    }
};
