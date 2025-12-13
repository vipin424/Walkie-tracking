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
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedInteger('total_days')->default(1)->after('event_to');
            $table->enum('extra_charge_type', ['delivery', 'staff'])->nullable()->after('total_days');
            $table->decimal('extra_charge_rate', 10, 2)->default(0)->after('extra_charge_type');
            $table->decimal('extra_charge_total', 10, 2)->default(0)->after('extra_charge_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['total_days', 'extra_charge_type', 'extra_charge_rate', 'extra_charge_total']);
        });
    }
};
