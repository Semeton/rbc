<?php

declare(strict_types=1);

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
        Schema::table('daily_truck_records', function (Blueprint $table): void {
            $table->decimal('customer_cost', 15, 2)->default(0)->after('load_dispatch_date');
            $table->decimal('incentive', 15, 2)->default(0)->after('gas_chop_money');
            $table->decimal('salary_contribution', 15, 2)->default(0)->after('incentive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_truck_records', function (Blueprint $table): void {
            $table->dropColumn(['customer_cost', 'incentive', 'salary_contribution']);
        });
    }
};


