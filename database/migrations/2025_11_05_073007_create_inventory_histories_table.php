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
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories');
            $table->foreignId('distributor_id')->nullable()->constrained('distributors');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('inventory_type_id')->nullable()->constrained('inventory_types');
            $table->enum('invinorout', ['in', 'out']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('reordering_level')->default(0);
            $table->string('unit');
            $table->integer('quantity_sold')->default(0);
            $table->decimal('cost_price_sold', 10, 2)->default(0);
            $table->decimal('sale_price_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->integer('return_quantity')->default(0);
            $table->foreignId('inventory_group_id')->nullable()->constrained('inventory_history_groups');
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
        Schema::dropIfExists('inventory_histories');
    }
};
