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
        Schema::create('inventory_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_out_id')->constrained('inventory_histories');
            $table->text('remarks')->nullable();
            $table->integer('quantity')->default(0);
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
        Schema::dropIfExists('inventory_returns');
    }
};
