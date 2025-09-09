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
        Schema::create('gift_card_shares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gift_card_id')->index();
            $table->unsignedBigInteger('from_user_id')->index();
            $table->unsignedBigInteger('to_user_id')->nullable()->index();
            $table->string('to_email')->nullable();
            $table->string('share_token', 128)->index()->unique();
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, cancelled
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_card_shares');
    }
};
