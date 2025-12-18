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
            $table->decimal('security_deposit',10,2)->nullable();     // Deposit collected
            $table->decimal('damage_charge',10,2)->nullable();        // If damaged
            $table->decimal('late_fee',10,2)->nullable();             // If returned late
            $table->decimal('refund_amount',10,2)->nullable();        // Refund to customer
            $table->decimal('amount_due',10,2)->nullable();           // customer needs to pay
            $table->enum('settlement_status',['pending','settled'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'security_deposit',
                'damage_charge',
                'late_fee',
                'refund_amount',
                'amount_due',
                'settlement_status'
            ]);
        });
    }
};
