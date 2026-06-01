<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('order_item_id')
                  ->constrained('order_items')
                  ->cascadeOnDelete();

            $table->integer('returned_qty');

            $table->date('return_date');

            $table->enum('condition', ['good', 'damaged', 'missing'])
                  ->default('good');

            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')
                  ->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_return_items');
    }
};
