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
            $table->text('bill_to')->nullable()->after('notes');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->text('bill_to')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('bill_to');
        });
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('bill_to');
        });
    }
};
