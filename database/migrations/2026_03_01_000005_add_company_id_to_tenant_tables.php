<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['clients', 'orders', 'quotations', 'invoices'] as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreignId('company_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['clients', 'orders', 'quotations', 'invoices'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['company_id']);
                    $t->dropColumn('company_id');
                });
            }
        }
    }
};
