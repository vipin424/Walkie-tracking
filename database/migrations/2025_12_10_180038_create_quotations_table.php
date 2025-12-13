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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->date('event_from')->nullable();
            $table->date('event_to')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 12,2)->default(0);
            $table->decimal('tax_amount', 12,2)->default(0);
            $table->decimal('discount_amount', 12,2)->default(0);
            $table->decimal('total_amount', 12,2)->default(0);
            $table->enum('status',['draft','sent','accepted','rejected'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
