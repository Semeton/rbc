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
        Schema::create('daily_truck_records', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('truck_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->timestamp('atc_collection_date');
            $table->timestamp('load_dispatch_date');
            $table->float('fare')->nullable();
            $table->float('gas_chop_money')->nullable();
            $table->float('balance')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_truck_records');
    }
};
