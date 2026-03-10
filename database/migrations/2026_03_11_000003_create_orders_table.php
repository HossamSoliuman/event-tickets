<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('total_amount');

            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])
                  ->default('pending');

            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_payment_status')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('stripe_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
