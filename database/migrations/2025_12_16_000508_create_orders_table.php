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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_code')->unique();

            // quotation optional
            $table->foreignId('quotation_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // client
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();

            // event
            $table->date('event_from')->nullable();
            $table->date('event_to')->nullable();
            $table->integer('total_days')->default(1);

            // amounts
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);

            $table->string('extra_charge_type')->nullable(); // delivery / staff
            $table->decimal('extra_charge_rate', 12, 2)->default(0);
            $table->decimal('extra_charge_total', 12, 2)->default(0);

            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            // payments
            $table->decimal('advance_paid', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);

            $table->enum('status', ['confirmed','dispatched','completed','cancelled'])
                ->default('confirmed');

            $table->foreignId('created_by')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
