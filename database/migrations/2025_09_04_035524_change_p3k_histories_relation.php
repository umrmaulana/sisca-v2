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
            if (!Schema::hasColumn('tt_p3k_histories', 'p3k_id')) {
                $table->unsignedBigInteger('p3k_id')->after('id');

                // Tambahkan foreign key jika ingin relasi aman
                $table->foreign('p3k_id')->references('id')->on('tm_p3k')->onDelete('cascade');
            }
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
            //
        });
    }
};
