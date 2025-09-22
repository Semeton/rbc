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
        Schema::table('daily_customer_transactions', function (Blueprint $table) {
            $table->decimal('tons', 10, 2)->after('transport_cost')->comment('Number of tons for this transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_customer_transactions', function (Blueprint $table) {
            $table->dropColumn('tons');
        });
    }
};
