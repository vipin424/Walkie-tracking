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
        Schema::create('order_agreements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();

                $table->string('agreement_code')->unique();
                $table->string('aadhaar_front')->nullable();
                $table->string('aadhaar_back')->nullable();
                $table->string('aadhaar_full')->nullable();

                $table->timestamp('aadhaar_uploaded_at')->nullable();
                $table->unsignedBigInteger('aadhaar_uploaded_by')->nullable();
                $table->string('signature_image')->nullable();

                $table->string('aadhaar_status')->default('pending'); 
                $table->enum('status', ['pending', 'signed', 'expired'])->default('pending');
                $table->timestamp('signed_at')->nullable();
                $table->timestamp('expires_at'); // 🔑 LINK EXPIRY

                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_agreements');
    }
};
