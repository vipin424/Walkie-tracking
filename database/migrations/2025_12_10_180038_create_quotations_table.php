<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();

            // Client link (optional FK for CRM)
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();

            // Client Info (denormalized for portability)
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('bill_to')->nullable();

            // Event Details
            $table->date('event_from')->nullable();
            $table->date('event_to')->nullable();
            $table->integer('total_days')->default(1);
            $table->string('handle_type')->nullable();   // 'self' | 'staff'
            $table->string('pickup_type')->nullable();   // 'pickup' | 'delivery'
            $table->integer('staff_count')->nullable()->default(1);
            $table->text('notes')->nullable();
            $table->date('valid_until')->nullable();     // Quotation expiry

            // Financials
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->string('extra_charge_type')->nullable();   // 'delivery' | 'staff'
            $table->decimal('extra_charge_rate', 12, 2)->default(0);
            $table->decimal('extra_charge_total', 12, 2)->default(0);
            $table->decimal('travelling_charge', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            // Status & Meta
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->string('pdf_path')->nullable();
            $table->string('created_by')->nullable();

            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
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

        Schema::create('quotation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->string('user')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_logs');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
