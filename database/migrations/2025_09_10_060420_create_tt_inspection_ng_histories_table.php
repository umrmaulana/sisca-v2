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
        Schema::create('tt_inspection_ng_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_inspection_id');
            $table->unsignedBigInteger('equipment_id');
            $table->unsignedBigInteger('checksheet_id');
            $table->unsignedBigInteger('user_id'); // PIC yang melakukan inspection
            $table->date('inspection_date');
            $table->string('picture')->nullable();
            $table->enum('status', ['OK', 'NG', 'N/A'])->default('NG');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('original_inspection_id')->references('id')->on('tt_inspections')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('tm_equipments')->onDelete('cascade');
            $table->foreign('checksheet_id')->references('id')->on('tm_checksheet_templates')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tt_inspection_ng_histories');
    }
};
