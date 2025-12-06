<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_history_groups', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('grand_total_amount');
            $table->text('customer_address')->nullable()->after('customer_name');
            $table->string('customer_phone')->nullable()->after('customer_address');
            $table->string('payment_method')->default('Cashless')->after('customer_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_history_groups', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_address', 'customer_phone', 'payment_method']);
        });
    }
};
