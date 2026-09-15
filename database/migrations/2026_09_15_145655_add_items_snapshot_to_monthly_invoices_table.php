<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_invoices', function (Blueprint $table) {
            // Stores per-item snapshot with computed amounts (including pro-rated rows) for invoice PDF
            $table->text('items_snapshot')->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_invoices', function (Blueprint $table) {
            $table->dropColumn('items_snapshot');
        });
    }
};

