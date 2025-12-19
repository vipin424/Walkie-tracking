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
            $table->decimal('security_deposit',10,2)->nullable()->after('pdf_path');     // Deposit collected
            $table->decimal('damage_charge',10,2)->nullable()->after('security_deposit');        
            $table->decimal('deposit_adjusted',10,2)->nullable()->after('damage_charge'); 
            $table->decimal('late_fee',10,2)->nullable()->after('deposit_adjusted');             // If returned late
            $table->decimal('refund_amount',10,2)->nullable()->after('late_fee');        // Refund to customer
            $table->decimal('amount_due',10,2)->nullable()->after('refund_amount');           // customer needs to pay
            $table->enum('settlement_status',['pending','settled'])->default('pending')->after('amount_due'); // Settlement status
            $table->date('settlement_date')->nullable()->after('settlement_status');
            $table->decimal('final_payable',10,2)->nullable()->after('settlement_date'); // Final amount payable after settlement
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
                'deposit_adjusted',
                'late_fee',
                'refund_amount',
                'amount_due',
                'settlement_status',
                'settlement_date',
                'final_payable'
            ]);
        });
    }
};
