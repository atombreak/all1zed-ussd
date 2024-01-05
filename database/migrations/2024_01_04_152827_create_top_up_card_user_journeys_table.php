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
        Schema::create('top_up_card_user_journeys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone_number');
            $table->string('payer_phone_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string('txn_amount')->nullable();
            $table->string('pin')->nullable();
            $table->string('account_type')->default('physical');
            $table->integer('step')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_up_card_user_journeys');
    }
};
