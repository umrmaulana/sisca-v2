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
        Schema::table('tm_companies', function (Blueprint $table) {
            $table->string('company_mapping_picture')->nullable()->after('company_name');
            $table->text('company_description')->nullable()->after('company_mapping_picture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_companies', function (Blueprint $table) {
            $table->dropColumn(['company_mapping_picture', 'company_description']);
        });
    }
};