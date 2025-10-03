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
        Schema::table('tm_p3k', function (Blueprint $table) {
            $table->date('expired_at')->nullable()->after('actual_stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tm_p3k', function (Blueprint $table) {
            $table->dropColumn('expired_at')->null;
        });
    }
};
