<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Calva Pharma');
            $table->string('company_logo')->nullable()->default('assets/images/calva.jpg');
            $table->text('company_address')->default('123 Main Street, City, Province, ZIP Code');
            $table->string('company_phone')->nullable()->default('(123) 456-7890');
            $table->string('company_email')->nullable()->default('bcdotg@gmail.com');
            $table->string('company_tax_id')->nullable()->default('409-604-053-00000');
            $table->timestamps();
        });

        // Insert default company settings
        DB::table('company_settings')->insert([
            'company_name' => 'Calva Pharma',
            'company_logo' => 'assets/images/calva.jpg',
            'company_address' => '123 Main Street, City, Province, ZIP Code',
            'company_phone' => '(123) 456-7890',
            'company_email' => 'bcdotg@gmail.com',
            'company_tax_id' => '409-604-053-00000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_settings');
    }
};
