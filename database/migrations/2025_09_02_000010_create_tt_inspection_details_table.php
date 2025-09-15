<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tt_inspection_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('tt_inspections')->onDelete('cascade');
            $table->foreignId('checksheet_id')->constrained('tm_checksheet_templates');
            $table->string('picture')->nullable();
            $table->enum('status', ['OK', 'NG', 'NA'])->nullable(); // keeping ERD field name
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tt_inspection_details');
    }
};
