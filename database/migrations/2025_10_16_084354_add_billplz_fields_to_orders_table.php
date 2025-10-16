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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('billplz_bill_id')->nullable()->after('status');
            $table->string('billplz_collection_id')->nullable()->after('billplz_bill_id');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('billplz_collection_id');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->string('idempotency_key')->nullable()->unique()->after('paid_at');
            $table->json('payment_metadata')->nullable()->after('idempotency_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'billplz_bill_id',
                'billplz_collection_id',
                'payment_status',
                'paid_at',
                'idempotency_key',
                'payment_metadata',
            ]);
        });
    }
};
