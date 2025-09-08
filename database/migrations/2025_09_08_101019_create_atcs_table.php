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
        Schema::create('atcs', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('company');
            $table->bigInteger('atc_number')->index()->unique();
            $table->enum('atc_type', ['bg', 'cash_payment']);
            $table->float('amount');
            $table->bigInteger('tons');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atcs');
    }
};
