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
        Schema::table('monthly_subscription_agreements', function (Blueprint $table) {
            $table->decimal('security_deposit', 10, 2)->nullable()->after('agreement_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_subscription_agreements', function (Blueprint $table) {
            $table->dropColumn('security_deposit');
        });
    }
};
