<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────
        // PAYMENT TRANSACTIONS (polymorphic — orders & monthly invoices)
        // ─────────────────────────────────────────────
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();

            // Polymorphic — can be Order or MonthlyInvoice
            $table->nullableMorphs('payable');                      // payable_type, payable_id

            // Keep order_id for direct order queries (redundant but useful for reporting)
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['gpay', 'paytm', 'phonepe', 'cash', 'bank_transfer', 'upi', 'other'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('recorded_by')->nullable();

            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // PAYMENT REMINDERS (log of reminders sent)
        // ─────────────────────────────────────────────
        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['whatsapp', 'email', 'both']);
            $table->text('message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_reminders');
        Schema::dropIfExists('payment_transactions');
    }
};
