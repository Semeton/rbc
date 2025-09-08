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
        // Remove UUID default values that don't work with SQLite
        $tables = [
            'users',
            'customers',
            'drivers',
            'trucks',
            'atcs',
            'daily_customer_transactions',
            'daily_truck_records',
            'customer_payments',
            'truck_maintenance_records',
            'audit_trails',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $table) {
                    // Drop the unique index first
                    $table->dropUnique(['uuid']);
                    // Then drop the column
                    $table->dropColumn('uuid');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add UUID columns back
        $tables = [
            'users',
            'customers',
            'drivers',
            'trucks',
            'atcs',
            'daily_customer_transactions',
            'daily_truck_records',
            'customer_payments',
            'truck_maintenance_records',
            'audit_trails',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->uuid('uuid')->unique()->after('id');
                });
            }
        }
    }
};
