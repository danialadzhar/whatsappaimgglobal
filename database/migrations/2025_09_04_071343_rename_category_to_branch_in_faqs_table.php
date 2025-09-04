<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename category column to branch
     */
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->renameColumn('category', 'branch');
        });
    }

    /**
     * Reverse the migrations.
     * Rename branch column back to category
     */
    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->renameColumn('branch', 'category');
        });
    }
};
