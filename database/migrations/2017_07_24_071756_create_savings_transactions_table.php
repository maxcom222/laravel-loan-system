<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('borrower_id')->unsigned()->nullable();
            $table->integer('savings_id')->unsigned()->nullable();
            $table->decimal('amount', 10, 2)->nullable()->default(0);
            $table->enum('type', array(
                'deposit',
                'withdrawal',
                'bank_fees',
                'interest',
                'dividend',
                'guarantee',
                'guarantee_restored'
            ))->nullable();
            $table->tinyInteger('system_interest')->default(0);
            $table->date('date')->nullable();
            $table->string('time')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->text('notes')->nullable();
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
        Schema::drop('savings_transactions');
    }
}
