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
            $table->foreignId('company_id')->nullable()->after('role')->constrained('tm_companies');
            $table->boolean('is_active')->default(true)->after('company_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key and new columns
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'is_active']);

            // Revert role back to enum
            $table->enum('role', ['Admin', 'User', 'Management', 'Supervisor', 'Pic'])->change();
        });
    }
};