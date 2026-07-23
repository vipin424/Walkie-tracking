<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            
            // This could be linked to an order, or null if it's an admin block (maintenance)
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->integer('quantity_reserved');
            
            // The actual physical lock dates (includes buffers)
            $table->date('locked_from');
            $table->date('locked_until');
            
            $table->string('reason')->default('customer_booking'); // e.g. customer_booking, maintenance, lost
            
            $table->timestamps();
            
            // Indexes for fast date overlap querying
            $table->index(['item_id', 'locked_from', 'locked_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_reservations');
    }
};
