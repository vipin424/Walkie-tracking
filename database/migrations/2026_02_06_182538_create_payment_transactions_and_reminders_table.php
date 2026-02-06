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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['gpay', 'paytm', 'phonepe', 'cash', 'bank_transfer', 'upi', 'other'])->default('other');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at');
            $table->string('recorded_by')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('channel', ['whatsapp', 'email', 'both']);
            $table->text('message')->nullable();
            $table->timestamp('sent_at');
            $table->string('sent_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reminders');
        Schema::dropIfExists('payment_transactions');
    }
};
