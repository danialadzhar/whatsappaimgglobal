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
        Schema::create('ai_activations', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true); // Status chatbot AI aktif atau tidak
            $table->string('name')->default('chatbot'); // Nama setting (untuk future expansion)
            $table->text('description')->nullable(); // Deskripsi setting
            $table->timestamp('last_updated_at')->nullable(); // Bila kali terakhir dikemas kini
            $table->timestamps();

            // Index untuk performance
            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_activations');
    }
};
