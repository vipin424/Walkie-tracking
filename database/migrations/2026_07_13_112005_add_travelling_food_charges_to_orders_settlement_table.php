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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('settlement_travelling_charges', 10, 2)->nullable()->default(0)->after('late_fee');
            $table->decimal('settlement_food_charges', 10, 2)->nullable()->default(0)->after('settlement_travelling_charges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['settlement_travelling_charges', 'settlement_food_charges']);
        });
    }
};
