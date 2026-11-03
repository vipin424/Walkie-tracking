<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('quotation_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->date('event_date');
            $table->string('label')->nullable(); // e.g. "Day 1", or custom label
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('quotation_dates');
    }
};
