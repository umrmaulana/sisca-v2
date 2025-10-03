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
            $table->dropColumn('accident_name');
            $table->foreignId('accident_id')
                ->nullable()
                ->after('department_id')
                ->constrained('tm_accident')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tt_accidents', function (Blueprint $table) {
            $table->dropForeign(['accident_id']);
            $table->dropColumn('accident_id');
            $table->string('accident_name')->nullable();
        });
    }

};
