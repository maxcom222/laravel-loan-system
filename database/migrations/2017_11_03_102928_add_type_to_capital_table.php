<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToCapitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capital', function ($table) {
            $table->enum('type', ['withdrawal', 'deposit'])->default('deposit');
            $table->integer('loan_id')->nullable();
            $table->integer('expense_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('capital', function ($table) {
            $table->dropColumn([
                'type',
                'loan_id',
                'expense_id'
            ]);
        });
    }
}
