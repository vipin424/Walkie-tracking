<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('payable_type')->nullable()->after('id');
            $table->unsignedBigInteger('payable_id')->nullable()->after('payable_type');
            
            // Make order_id nullable since we'll use polymorphic relationship
            $table->foreignId('order_id')->nullable()->change();
            
            // Add index for polymorphic relationship
            $table->index(['payable_type', 'payable_id']);
        });
    }

    public function down()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropIndex(['payable_type', 'payable_id']);
            $table->dropColumn(['payable_type', 'payable_id']);
        });
    }
};
