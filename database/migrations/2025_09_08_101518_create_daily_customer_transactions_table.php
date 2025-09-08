<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->default(DB::raw('(UUID())'));
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('atc_id')->constrained();
            $table->timestamp('date');
            $table->string('origin');
            $table->string('deport_details');
            $table->string('cement_type');
            $table->string('destination');
            $table->float('atc_cost');
            $table->float('transport_cost');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_customer_transactions');
    }
};
