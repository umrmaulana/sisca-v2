<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tm_checksheet_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_type_id')->constrained('tm_equipment_types');
            $table->unsignedBigInteger('order_number');
            $table->string('item_name');
            $table->string('standar_condition')->nullable();
            $table->string('standar_picture')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_checksheet_templates');
    }
};
