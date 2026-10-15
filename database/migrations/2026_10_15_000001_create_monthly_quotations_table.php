<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();

            // Client Info (free text — no FK)
            $table->string('client_name');
            $table->string('client_company')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->text('client_address')->nullable();
            $table->string('client_gstin')->nullable();

            // Bill To block (formatted text for PDF)
            $table->text('bill_to')->nullable();

            // Quotation terms
            $table->integer('validity_days')->default(30);
            $table->string('payment_terms')->nullable();
            $table->string('delivery_timeline')->nullable();

            // Financials
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            // Misc
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft, sent
            $table->string('pdf_path')->nullable();
            $table->string('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_quotations');
    }
};
