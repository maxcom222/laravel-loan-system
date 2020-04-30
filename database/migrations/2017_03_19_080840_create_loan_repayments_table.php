<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRepaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id')->unsigned();
            $table->integer('borrower_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned();
            $table->decimal('amount', 10, 4);
            $table->integer('repayment_method_id')->unsigned();
            $table->date('collection_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loan_repayments');
    }
}
