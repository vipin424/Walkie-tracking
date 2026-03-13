<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_code')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone');
            $table->date('billing_start_date');
            $table->integer('billing_day_of_month')->default(1);
            $table->decimal('monthly_amount', 10, 2);
            $table->text('items_json');
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->timestamps();
        });

        Schema::create('monthly_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('monthly_subscriptions')->cascadeOnDelete();
            $table->string('invoice_code')->unique();
            $table->date('billing_period_from');
            $table->date('billing_period_to');
            $table->decimal('amount', 10, 2);
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['pending', 'sent', 'paid'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_invoices');
        Schema::dropIfExists('monthly_subscriptions');
    }
};
