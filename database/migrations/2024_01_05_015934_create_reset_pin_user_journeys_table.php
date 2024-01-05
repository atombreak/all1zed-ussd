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
        Schema::create('reset_pin_user_journeys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone_number');
            $table->string('current_pin')->nullable();
            $table->string('new_pin')->nullable();
            $table->integer('step')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reset_pin_user_journeys');
    }
};
