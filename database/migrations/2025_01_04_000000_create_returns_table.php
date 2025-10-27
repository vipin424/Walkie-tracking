<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dispatch_item_id')->constrained('dispatch_items')->cascadeOnDelete();
            $table->unsignedInteger('returned_qty');
            $table->date('return_date');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('returns');
    }
};
