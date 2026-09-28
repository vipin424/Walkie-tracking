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
        Schema::table('damage_reports', function (Blueprint $table) {
            // New: JSON array of walkie units [{model, serial, unit_id}]
            $table->json('walkie_units')->nullable()->after('billing_details');

            // Drop old single-unit columns (data migrated to walkie_units)
            $table->dropColumn(['walkie_model', 'walkie_serial', 'walkie_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->string('walkie_model')->nullable()->after('billing_details');
            $table->string('walkie_serial')->nullable()->after('walkie_model');
            $table->string('walkie_unit_id')->nullable()->after('walkie_serial');
            $table->dropColumn('walkie_units');
        });
    }
};
