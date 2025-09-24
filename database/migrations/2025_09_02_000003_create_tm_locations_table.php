<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tm_locations_new', function (Blueprint $table) {
            $table->id();
            $table->char('location_code', 20)->unique();
            $table->foreignId('company_id')->constrained('tm_companies');
            $table->foreignId('area_id')->constrained('tm_areas');
            $table->string('pos')->nullable();
            $table->decimal('coordinate_x', 10, 6)->nullable();
            $table->decimal('coordinate_y', 10, 6)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_locations');
    }
};
