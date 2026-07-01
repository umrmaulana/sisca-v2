<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tt_inspections', function (Blueprint $table) {
            $table->index(['equipment_id', 'inspection_date', 'status'], 'tt_inspections_equipment_date_status_idx');
            $table->index(['inspection_date', 'status'], 'tt_inspections_date_status_idx');
        });

        Schema::table('tt_inspection_details', function (Blueprint $table) {
            $table->index(['inspection_id', 'status'], 'tt_inspection_details_inspection_status_idx');
        });

        Schema::table('tm_equipments', function (Blueprint $table) {
            $table->index(['is_active', 'equipment_type_id', 'location_id'], 'tm_equipments_active_type_location_idx');
        });

        Schema::table('tm_locations_new', function (Blueprint $table) {
            $table->index(['company_id', 'area_id'], 'tm_locations_company_area_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tt_inspections', function (Blueprint $table) {
            $table->dropIndex('tt_inspections_equipment_date_status_idx');
            $table->dropIndex('tt_inspections_date_status_idx');
        });

        Schema::table('tt_inspection_details', function (Blueprint $table) {
            $table->dropIndex('tt_inspection_details_inspection_status_idx');
        });

        Schema::table('tm_equipments', function (Blueprint $table) {
            $table->dropIndex('tm_equipments_active_type_location_idx');
        });

        Schema::table('tm_locations_new', function (Blueprint $table) {
            $table->dropIndex('tm_locations_company_area_idx');
        });
    }
};
