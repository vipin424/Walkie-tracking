<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_addendum_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('monthly_subscriptions')->onDelete('cascade');
            $table->string('addendum_code')->unique();              // e.g. ADD-20260915-123
            $table->date('effective_date');                        // date new items added
            $table->text('new_items_json');                        // snapshot of ONLY new items
            $table->decimal('pro_rated_amount', 10, 2);           // total pro-rated charge this cycle
            $table->decimal('new_monthly_amount', 10, 2);         // new full monthly total after addition
            $table->integer('billing_day_of_month');
            $table->integer('pro_rated_days');                     // how many days pro-rated for
            $table->date('pro_rated_until');                       // billing date (end of pro-rate window)
            $table->string('signed_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_image')->nullable();
            $table->string('signed_pdf')->nullable();
            $table->enum('status', ['pending', 'signed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_addendum_agreements');
    }
};

