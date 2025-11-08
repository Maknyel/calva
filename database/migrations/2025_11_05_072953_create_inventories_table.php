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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->nullable()->constrained('distributors');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('inventory_type_id')->constrained('inventory_types');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('reordering_level')->default(0);
            $table->string('unit');
            $table->integer('current_quantity')->default(0);
            $table->decimal('current_cost_price', 10, 2)->default(0);
            $table->decimal('current_sale_price', 10, 2)->default(0);
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
        Schema::dropIfExists('inventories');
    }
};
