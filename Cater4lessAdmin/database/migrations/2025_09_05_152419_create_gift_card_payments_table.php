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
        Schema::create('gift_card_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable(false);
            $table->decimal('amount', 12, 2);
            $table->string('payment_status')->default('pending'); // pending, success, failed
            $table->string('payment_method')->nullable();
            $table->string('payment_platform')->nullable();
            $table->string('gateway_reference')->nullable(); // external gateway id
            $table->text('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_card_payments');
    }
};
