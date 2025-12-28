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
        Schema::table('order_agreements', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('expires_at');
            $table->text('signed_url')->nullable()->after('sent_at');
            $table->string('signed_pdf')->nullable()->after('signed_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_agreements', function (Blueprint $table) {
            $table->dropColumn('sent_at','signed_url','signed_pdf');
        });
    }
};
