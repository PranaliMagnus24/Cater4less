<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('gift_cards', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->decimal('amount', 10, 2);
        $table->decimal('balance', 10, 2);
        $table->date('expiry_date')->nullable();
        $table->enum('status', ['active', 'redeemed', 'expired'])->default('active');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
