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
            $table->tinyInteger('handle_type')->default(0)->comment('0 = Our_Staff, 1 = Client_(Self)')->after('event_to');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->tinyInteger('handle_type')->default(0)->comment('0 = Our_Staff, 1 = Client_(Self)')->after('event_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('handle_type');
        });
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('handle_type');
        });
    }
};
