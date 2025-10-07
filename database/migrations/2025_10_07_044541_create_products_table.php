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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews')->default(0);
            $table->json('colors')->nullable(); // Store as JSON array
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->string('brand')->nullable();
            $table->json('specifications')->nullable(); // Store product specs as JSON
            $table->text('tags')->nullable(); // Comma-separated tags
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
