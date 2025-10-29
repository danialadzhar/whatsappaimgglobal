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
        Schema::table('products', function (Blueprint $table) {
            // Tambah normal_price column
            $table->decimal('normal_price', 10, 2)->nullable()->after('price');

            // Tambah color column (single color string)
            $table->string('color')->nullable()->after('colors');

            // Tambah storage column
            $table->string('storage')->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Buang columns yang ditambah
            $table->dropColumn(['normal_price', 'color', 'storage']);
        });
    }
};
