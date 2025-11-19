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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->unsignedBigInteger('client_id');
            $table->date('order_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('event_name')->nullable();
            $table->string('location')->nullable();
            $table->enum('delivery_type', ['pickup','delivery'])->default('pickup');
            $table->decimal('delivery_charges', 10, 2)->default(0);
            $table->enum('status', ['pending','approved','rejected','dispatched','completed'])->default('pending');
            $table->date('reminder_date')->nullable(); // auto 2-day-before alert
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
