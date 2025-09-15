<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change role from enum to varchar
            $table->string('role')->change();

            // Add new columns
            $table->foreignId('plant_id')->nullable()->after('role')->constrained('tm_plants');
            $table->boolean('is_active')->default(true)->after('plant_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key and new columns
            $table->dropForeign(['plant_id']);
            $table->dropColumn(['plant_id', 'is_active']);

            // Revert role back to enum
            $table->enum('role', ['Admin', 'User', 'MTE'])->change();
        });
    }
};