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
        Schema::create('tt_p3k_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p3k_id')->constrained('tm_p3k')->onDelete('cascade');
            $table->string('npk');
            $table->enum('action', ['add', 'remove', 'restock', 'take']);
            $table->integer('quantity');
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
        Schema::dropIfExists('tt_p3k_histories');
    }
};
