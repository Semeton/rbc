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
        Schema::table('daily_truck_records', function (Blueprint $table) {
            if (! Schema::hasColumn('daily_truck_records', 'atc_id')) {
                $table->unsignedBigInteger('atc_id')->nullable()->after('customer_id');
            }

            if (! Schema::hasColumn('daily_truck_records', 'haulage')) {
                $table->decimal('haulage', 15, 2)->nullable()->after('gas_chop_money');
            }
        });

        // Foreign key intentionally omitted to avoid constraint issues with existing data.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_truck_records', function (Blueprint $table) {
            if (Schema::hasColumn('daily_truck_records', 'atc_id')) {
                $table->dropForeign(['atc_id']);
                $table->dropColumn('atc_id');
            }

            if (Schema::hasColumn('daily_truck_records', 'haulage')) {
                $table->dropColumn('haulage');
            }
        });
    }
};
