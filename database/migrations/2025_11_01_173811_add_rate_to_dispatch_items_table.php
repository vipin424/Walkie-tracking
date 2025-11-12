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
        Schema::table('dispatch_items', function (Blueprint $table) {
            $table->decimal('rate_per_day', 10, 2)->default(0)->after('returned_qty');
            $table->decimal('total_amount', 10, 2)->default(0)->after('rate_per_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_items', function (Blueprint $table) {
                $table->dropColumn(['rate_per_day','total_amount']);
        });
    }
};
