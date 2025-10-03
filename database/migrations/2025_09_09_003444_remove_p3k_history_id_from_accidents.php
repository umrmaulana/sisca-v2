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
    public function up(): void
    {
        Schema::table('tt_accidents', function (Blueprint $table) {
            // Hapus foreign key dulu sebelum drop kolom
            $table->dropForeign(['p3k_history_id']);
            $table->dropColumn('p3k_history_id');
        });
    }

    public function down(): void
    {
        Schema::table('tm_accidents', function (Blueprint $table) {
            $table->foreignId('p3k_history_id')->nullable()->constrained()->onDelete('set null');
        });
    }
};
