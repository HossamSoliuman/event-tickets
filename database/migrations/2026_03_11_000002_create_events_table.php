<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('venue');
            $table->dateTime('date');
            $table->unsignedInteger('total_tickets');
            $table->unsignedInteger('available_tickets');
            $table->unsignedInteger('price');
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['date', 'is_active']);
            $table->index('available_tickets');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
