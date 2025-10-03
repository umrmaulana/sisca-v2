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
        Schema::create('tt_accidents', function (Blueprint $table) {
            $table->id();
            $table->string('accident_name');
            $table->foreignId('location_id')->constrained('tm_p3k_location')->onDelete('cascade');
            $table->string('reported_by');
            $table->string('victim_name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('p3k_history_id')->nullable()->constrained('tt_p3k_histories')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tt_accidents');
    }
};
