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
        Schema::create('inventory_history_groups', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('dr_number')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discounted_amount', 10, 2)->default(0);
            $table->decimal('grand_total_amount', 10, 2)->default(0);
            $table->timestamp('date_time_adjustment')->useCurrent();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_history_groups');
    }
};
