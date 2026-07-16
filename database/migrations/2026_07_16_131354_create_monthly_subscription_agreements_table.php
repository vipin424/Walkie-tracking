<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_subscription_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('monthly_subscriptions')->onDelete('cascade');
            $table->string('agreement_code')->unique();
            $table->date('agreement_start_date');
            $table->date('agreement_end_date');
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
        Schema::dropIfExists('monthly_subscription_agreements');
    }
};
