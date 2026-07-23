<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────
        // MONTHLY SUBSCRIPTIONS
        // ─────────────────────────────────────────────
        Schema::create('monthly_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_code')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();

            // Client Info (denormalized)
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone');
            $table->string('cc_emails')->nullable();    // comma-separated CC emails

            // Billing Config
            $table->date('billing_start_date');
            $table->integer('billing_day_of_month')->default(1);  // 1–28
            $table->decimal('monthly_amount', 12, 2);
            $table->text('items_json');                 // JSON list of items

            // Info
            $table->text('billing_details')->nullable(); // GST/invoice address details
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');

            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // MONTHLY INVOICES (auto-generated per billing cycle)
        // ─────────────────────────────────────────────
        Schema::create('monthly_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('monthly_subscriptions')->cascadeOnDelete();
            $table->string('invoice_code')->unique();
            $table->date('billing_period_from');
            $table->date('billing_period_to');
            $table->decimal('amount', 12, 2);
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['pending', 'sent', 'paid'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // MONTHLY SUBSCRIPTION AGREEMENTS
        // ─────────────────────────────────────────────
        Schema::create('monthly_subscription_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('monthly_subscriptions')->cascadeOnDelete();
            $table->string('agreement_code')->unique();

            // Agreement Period
            $table->date('agreement_start_date')->nullable();
            $table->date('agreement_end_date')->nullable();
            $table->decimal('security_deposit', 12, 2)->nullable()->default(0);

            // Signing
            $table->string('signed_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_image')->nullable();
            $table->string('signed_pdf')->nullable();

            $table->string('status')->default('pending');   // pending | signed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_subscription_agreements');
        Schema::dropIfExists('monthly_invoices');
        Schema::dropIfExists('monthly_subscriptions');
    }
};
