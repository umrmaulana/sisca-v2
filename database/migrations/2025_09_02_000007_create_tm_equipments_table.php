<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tm_equipments', function (Blueprint $table) {
            $table->id();
            $table->char('equipment_code', 20)->unique();
            $table->foreignId('equipment_type_id')->constrained('tm_equipment_types');
            $table->foreignId('location_id')->constrained('tm_locations_new');
            $table->string('qrcode')->nullable();
            $table->foreignId('period_check_id')->nullable()->constrained('tm_period_checks');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_equipments');
    }
};
