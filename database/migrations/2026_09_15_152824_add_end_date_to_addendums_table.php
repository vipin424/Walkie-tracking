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
        Schema::table('subscription_addendum_agreements', function (Blueprint $table) {
            $table->date('agreement_end_date')->nullable()->after('effective_date');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_addendum_agreements', function (Blueprint $table) {
            $table->dropColumn('agreement_end_date');
        });
    }
};
