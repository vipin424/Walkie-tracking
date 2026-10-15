<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_quotation_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('uom')->default('MTH'); // MTH, Nos, Pcs, etc.
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('rate', 12, 2)->default(0); // per month rate
            $table->decimal('amount', 12, 2)->default(0); // qty * rate
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_quotation_items');
    }
};
