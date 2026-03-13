<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('monthly_subscriptions', function (Blueprint $table) {
            $table->text('cc_emails')->nullable()->after('client_email');
        });
    }

    public function down()
    {
        Schema::table('monthly_subscriptions', function (Blueprint $table) {
            $table->dropColumn('cc_emails');
        });
    }
};
