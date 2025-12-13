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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('item_type');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->integer('quantity');
            $table->enum('rental_type', ['daily','monthly'])->default('daily');
            $table->decimal('rate_per_day',10,2)->nullable();
            $table->decimal('rate_per_month',10,2)->nullable();
            $table->decimal('total_amount',10,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
