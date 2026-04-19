<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('travelling_charge', 10, 2)->default(0)->after('extra_charge_total');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('travelling_charge', 10, 2)->default(0)->after('extra_charge_total');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('travelling_charge');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('travelling_charge');
        });
    }
};
