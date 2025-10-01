<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('truck_maintenance_records', function (Blueprint $table) {
            if (! Schema::hasColumn('truck_maintenance_records', 'maintenance_date')) {
                $table->date('maintenance_date')->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('truck_maintenance_records', function (Blueprint $table) {
            if (Schema::hasColumn('truck_maintenance_records', 'maintenance_date')) {
                $table->dropColumn('maintenance_date');
            }
        });
    }
};
