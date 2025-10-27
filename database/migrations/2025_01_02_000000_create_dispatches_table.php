<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->date('dispatch_date');
            $table->date('expected_return_date')->nullable();
            $table->string('status', 30)->default('Active');
            $table->unsignedInteger('total_items')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('dispatches');
    }
};
