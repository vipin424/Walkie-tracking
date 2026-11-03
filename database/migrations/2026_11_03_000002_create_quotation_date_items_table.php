<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('quotation_date_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_date_id');
            $table->string('item_name');
            $table->string('item_type')->nullable();
            $table->string('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();
            $table->foreign('quotation_date_id')->references('id')->on('quotation_dates')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('quotation_date_items');
    }
};
