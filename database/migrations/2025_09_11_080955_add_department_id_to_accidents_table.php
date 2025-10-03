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
    public function up(): void
    {
        Schema::table('tt_accidents', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('location_id');

            $table->foreign('department_id')
                ->references('id')
                ->on('tm_departments')
                ->onDelete('set null'); // kalau departement dihapus, set NULL
        });
    }

    public function down(): void
    {
        Schema::table('tt_accidents', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }

};
