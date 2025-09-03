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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->text('customer_messages'); // Mesej dari customer
            $table->text('ai_messages'); // Mesej dari AI
            $table->unsignedBigInteger('customer_id'); // ID customer yang berkaitan
            $table->timestamps();

            // Foreign key constraint untuk customer_id
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            // Index untuk performance
            $table->index('customer_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
