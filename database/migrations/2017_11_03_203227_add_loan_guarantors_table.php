<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoanGuarantorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_guarantors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('loan_id')->unsigned()->nullable();
            $table->integer('borrower_id')->unsigned()->nullable();
            $table->integer('guarantor_id')->unsigned()->nullable();
            $table->integer('loan_application_id')->unsigned()->nullable();

        });
        //move current data in guarantors table to this table
        foreach (\App\Models\Guarantor::all() as $key){
            $b=new \App\Models\LoanGuarantor($key->id);
            $b->user_id=$key->user_id;
            $b->loan_id=$key->loan_id;
            $b->borrower_id=$key->borrower_id;
            $b->guarantor_id=$key->id;
            $b->loan_application_id=$key->loan_application_id;
            $b->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loan_guarantors');
    }
}
