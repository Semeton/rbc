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
        // Add soft deletes to models that need them
        $tables = [
            'customers',
            'drivers',
            'trucks',
            'atcs',
            'daily_customer_transactions',
            'daily_truck_records',
            'customer_payments',
            'truck_maintenance_records',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }

        // Add missing indexes for performance
        Schema::table('customers', function (Blueprint $table) {
            $table->index('status');
            $table->index('email');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->index('status');
            $table->index('company');
        });

        Schema::table('trucks', function (Blueprint $table) {
            $table->index('status');
            $table->index('year_of_manufacture');
        });

        Schema::table('atcs', function (Blueprint $table) {
            $table->index('status');
            $table->index('atc_type');
        });

        Schema::table('daily_customer_transactions', function (Blueprint $table) {
            $table->index('date');
            $table->index('status');
            $table->index(['customer_id', 'date']);
            $table->index(['driver_id', 'date']);
            $table->index(['atc_id', 'date']);
        });

        Schema::table('daily_truck_records', function (Blueprint $table) {
            $table->index('atc_collection_date');
            $table->index('load_dispatch_date');
            $table->index('status');
            $table->index(['driver_id', 'atc_collection_date']);
            $table->index(['truck_id', 'atc_collection_date']);
            $table->index(['customer_id', 'atc_collection_date']);
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->index('payment_date');
            $table->index(['customer_id', 'payment_date']);
        });

        Schema::table('truck_maintenance_records', function (Blueprint $table) {
            $table->index('status');
            $table->index(['truck_id', 'created_at']);
        });

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove soft deletes
        $tables = [
            'customers',
            'drivers',
            'trucks',
            'atcs',
            'daily_customer_transactions',
            'daily_truck_records',
            'customer_payments',
            'truck_maintenance_records',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }

        // Remove indexes
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['email']);
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['company']);
        });

        Schema::table('trucks', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['year_of_manufacture']);
        });

        Schema::table('atcs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['atc_type']);
        });

        Schema::table('daily_customer_transactions', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['customer_id', 'date']);
            $table->dropIndex(['driver_id', 'date']);
            $table->dropIndex(['atc_id', 'date']);
        });

        Schema::table('daily_truck_records', function (Blueprint $table) {
            $table->dropIndex(['atc_collection_date']);
            $table->dropIndex(['load_dispatch_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['driver_id', 'atc_collection_date']);
            $table->dropIndex(['truck_id', 'atc_collection_date']);
            $table->dropIndex(['customer_id', 'atc_collection_date']);
        });

        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropIndex(['payment_date']);
            $table->dropIndex(['customer_id', 'payment_date']);
        });

        Schema::table('truck_maintenance_records', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['truck_id', 'created_at']);
        });

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['module', 'created_at']);
            $table->dropIndex(['action', 'created_at']);
        });
    }
};
