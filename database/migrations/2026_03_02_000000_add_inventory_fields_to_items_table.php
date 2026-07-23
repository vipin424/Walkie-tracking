<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('total_stock')->default(0)->after('description');
            $table->decimal('security_deposit', 10, 2)->default(0)->after('unit_price');
            $table->integer('buffer_days_before')->default(0)->after('security_deposit')->comment('Days needed to prepare/deliver before rental starts');
            $table->integer('buffer_days_after')->default(0)->after('buffer_days_before')->comment('Days needed to pickup/inspect after rental ends');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'total_stock',
                'security_deposit',
                'buffer_days_before',
                'buffer_days_after'
            ]);
        });
    }
};
