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
        Schema::create('send_money_user_journeys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone_number');
            $table->string('txn_amount')->nullable();
            $table->string('account_type')->nullable();
            $table->string('card_type')->default('physical');
            $table->string('pin')->nullable();
            $table->string('receiver_card_number')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('reference')->nullable();
            $table->integer('step')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('send_money_user_journeys');
    }
};
