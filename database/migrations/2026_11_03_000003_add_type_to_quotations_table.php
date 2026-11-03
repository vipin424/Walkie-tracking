<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('quotations', function (Blueprint $table) {
            // 'standard' = old single date range, 'multi_date' = new per-date items
            $table->string('quotation_type')->default('standard')->after('status');
        });
    }
    public function down(): void {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('quotation_type');
        });
    }
};
