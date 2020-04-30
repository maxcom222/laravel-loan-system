<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountToExpenseAndIncomeCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_types', function ($table) {
            $table->integer('account_id')->nullable();
        });
        Schema::table('other_income_types', function ($table) {
            $table->integer('account_id')->nullable();
        });
        Schema::table('expenses', function ($table) {
            $table->integer('account_id')->nullable();
        });
        Schema::table('other_income', function ($table) {
            $table->integer('account_id')->nullable();
        });
        Schema::table('capital', function ($table) {
            $table->integer('account_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('expense_types', function ($table) {
            $table->dropColumn([
                'account_id'
            ]);
        });
        Schema::table('other_income_types', function ($table) {
            $table->dropColumn([
                'account_id'
            ]);
        });
        Schema::table('expenses', function ($table) {
            $table->dropColumn([
                'account_id'
            ]);
        });
        Schema::table('other_income', function ($table) {
            $table->dropColumn([
                'account_id'
            ]);
        });
        Schema::table('capital', function ($table) {
            $table->dropColumn([
                'account_id'
            ]);
        });
    }
}
