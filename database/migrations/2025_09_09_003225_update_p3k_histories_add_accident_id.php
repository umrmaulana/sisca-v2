<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tt_p3k_histories', function (Blueprint $table) {
            $table->foreignId('accident_id')->nullable()->after('p3k_id')->constrained('tt_accidents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tt_p3k_histories', function (Blueprint $table) {
            $table->dropForeign(['accident_id']);
            $table->dropColumn('accident_id');
        });
    }
};
