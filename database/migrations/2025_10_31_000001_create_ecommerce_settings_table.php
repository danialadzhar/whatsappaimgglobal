<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ecommerce_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('countdown_enabled')->default(true);
            $table->unsignedInteger('countdown_days')->default(3);
            $table->unsignedTinyInteger('countdown_hours')->default(11);
            $table->unsignedTinyInteger('countdown_minutes')->default(31);
            $table->text('urgency_text');
            $table->string('background_color', 20)->default('#1f2937');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_settings');
    }
};
