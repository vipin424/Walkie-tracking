<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────
        // ORDERS (main table — all fields consolidated)
        // ─────────────────────────────────────────────
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();

            // Links
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete(); // CRM link

            // Client Info (denormalized)
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('bill_to')->nullable();

            // Event Details
            $table->date('event_from')->nullable();
            $table->date('event_to')->nullable();
            $table->time('event_time')->nullable();
            $table->string('event_location')->nullable();
            $table->integer('total_days')->default(1);
            $table->boolean('handle_type')->default(0);  // 0 = staff, 1 = self pickup
            $table->string('pickup_type')->nullable();   // 'pickup' | 'delivery'
            $table->integer('staff_count')->nullable()->default(1);
            $table->text('notes')->nullable();
            $table->boolean('agreement_required')->default(false);

            // Pricing
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->string('extra_charge_type')->nullable();   // 'delivery' | 'staff'
            $table->decimal('extra_charge_rate', 12, 2)->default(0);
            $table->decimal('extra_charge_total', 12, 2)->default(0);
            $table->decimal('travelling_charge', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            // Payment Tracking
            $table->decimal('advance_paid', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            $table->decimal('final_payable', 12, 2)->default(0);
            $table->decimal('security_deposit', 12, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');

            // Settlement
            $table->decimal('damage_charge', 12, 2)->default(0);
            $table->decimal('late_fee', 12, 2)->default(0);
            $table->decimal('settlement_travelling_charges', 12, 2)->default(0);
            $table->decimal('settlement_food_charges', 12, 2)->default(0);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->decimal('deposit_adjusted', 12, 2)->default(0);
            $table->decimal('amount_due', 12, 2)->default(0);
            $table->enum('settlement_status', ['pending', 'settled'])->default('pending');
            $table->date('settlement_date')->nullable();

            // Status
            $table->enum('status', ['confirmed', 'sent', 'completed', 'cancelled'])->default('confirmed');
            $table->enum('return_status', ['pending', 'partial', 'fully_returned'])->default('pending');

            // Documents
            $table->string('pdf_path')->nullable();
            $table->string('settlement_pdf_path')->nullable();

            // Meta
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // ORDER ITEMS
        // ─────────────────────────────────────────────
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained()->nullOnDelete(); // catalog link
            $table->string('item_name');
            $table->string('item_type')->nullable();
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // ORDER AGREEMENTS (digital signature)
        // ─────────────────────────────────────────────
        Schema::create('order_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('agreement_code')->unique();

            // Signing
            $table->string('signed_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_image')->nullable();
            $table->string('signed_pdf')->nullable();

            // Aadhaar KYC
            $table->string('aadhaar_front')->nullable();
            $table->string('aadhaar_back')->nullable();
            $table->string('aadhaar_full')->nullable();
            $table->timestamp('aadhaar_uploaded_at')->nullable();
            $table->string('aadhaar_uploaded_by')->nullable();
            $table->string('aadhaar_status')->nullable();   // 'pending' | 'verified'

            $table->string('status')->default('pending');   // pending | signed
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // ORDER RETURN ITEMS (return tracking)
        // ─────────────────────────────────────────────
        Schema::create('order_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->integer('returned_qty');
            $table->date('return_date');
            $table->enum('condition', ['good', 'damaged', 'missing'])->default('good');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // ORDER LOGS (activity trail)
        // ─────────────────────────────────────────────
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_logs');
        Schema::dropIfExists('order_return_items');
        Schema::dropIfExists('order_agreements');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
