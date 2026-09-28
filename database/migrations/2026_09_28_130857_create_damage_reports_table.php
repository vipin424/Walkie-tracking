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
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique(); // DMG-YYYYMMDD-XXXX
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('monthly_subscriptions')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->string('billing_details')->nullable();

            // Walkie / device details
            $table->string('walkie_model')->nullable();
            $table->string('walkie_serial')->nullable();
            $table->string('walkie_unit_id')->nullable();

            // Damage details
            $table->text('damage_description');
            $table->date('damage_date');
            $table->json('damage_photos')->nullable(); // array of storage paths

            // Charges
            $table->decimal('replacement_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();

            // PDF / Status
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['pending', 'sent', 'paid'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};
