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
        // Add created_by to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        });

        // Add email and user_id to suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Add email and user_id to distributors table
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Add email and user_id to inventory_types table
        Schema::table('inventory_types', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Add user_id to inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Add user_id to inventory_histories table
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Add created_by and user_id to inventory_history_groups table
        Schema::table('inventory_history_groups', function (Blueprint $table) {
            $table->longText('created_by')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Add created_by and user_id to inventory_returns table
        Schema::table('inventory_returns', function (Blueprint $table) {
            $table->longText('created_by')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove columns from inventory_returns table
        Schema::table('inventory_returns', function (Blueprint $table) {
            // Check and drop foreign key constraint if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('inventory_returns');

            if (array_key_exists('inventory_returns_created_by_foreign', $indexesFound)) {
                $table->dropForeign(['created_by']);
            }
            $table->dropColumn('created_by');

            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Remove columns from inventory_history_groups table
        Schema::table('inventory_history_groups', function (Blueprint $table) {
            // Check and drop foreign key constraint if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('inventory_history_groups');

            if (array_key_exists('inventory_history_groups_created_by_foreign', $indexesFound)) {
                $table->dropForeign(['created_by']);
            }
            $table->dropColumn('created_by');

            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Remove user_id from inventory_histories table
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Remove user_id from inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Remove columns from inventory_types table
        Schema::table('inventory_types', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Remove columns from distributors table
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Remove columns from suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Remove created_by from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
