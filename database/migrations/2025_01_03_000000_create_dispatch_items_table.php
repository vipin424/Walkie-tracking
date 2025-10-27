<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained()->cascadeOnDelete();
            $table->string('item_type', 50);
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('returned_qty')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('dispatch_items');
    }
};
