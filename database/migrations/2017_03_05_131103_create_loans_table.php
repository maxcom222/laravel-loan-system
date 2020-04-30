<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('borrower_id');
            $table->integer('loan_product_id');
            $table->string('reference')->nullable();
            $table->date('release_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->date('interest_start_date')->nullable();
            $table->date('first_payment_date')->nullable();
            $table->integer('loan_disbursed_by_id')->nullable();
            $table->decimal('principal', 10, 4)->nullable();
            $table->enum('interest_method', array(
                'flat_rate',
                'declining_balance_equal_installments',
                'declining_balance_equal_principal',
                'interest_only',
                'compound_interest'
            ))->default('flat_rate');
            $table->decimal('interest_rate', 10, 4)->nullable();
            $table->enum('interest_period', array(
                'day',
                'week',
                'month',
                'year'
            ))->default('day');
            $table->tinyInteger('override_interest')->default(0);
            $table->decimal('override_interest_amount', 10, 4)->default(0.00)->nullable();
            $table->integer('loan_duration')->nullable();
            $table->enum('loan_duration_type', array(
                'day',
                'week',
                'month',
                'year'
            ))->default('year');
            $table->enum('repayment_cycle', array(
                'daily',
                'weekly',
                'monthly',
                'bi_monthly',
                'quarterly',
                'semi_annually',
                'annually'
            ))->default('monthly');
            $table->enum('decimal_places',
                array('round_off_to_two_decimal', 'round_off_to_integer'))->default('round_off_to_two_decimal');
            $table->string('repayment_order')->nullable();
            $table->enum('loan_fees_schedule', array(
                'dont_include',
                'distribute_fees_evenly',
                'charge_fees_on_released_date',
                'charge_fees_on_first_payment',
                'charge_fees_on_last_payment',
            ))->default('distribute_fees_evenly');
            $table->integer('grace_on_interest_charged')->nullable();
            $table->integer('loan_status_id')->nullable();
            $table->text('files')->nullable();
            $table->text('description')->nullable();
            $table->enum('loan_status', array(
                'open',
                'fully_paid',
                'defaulted',
                'restructured',
                'processing'
            ))->default('open');
            $table->decimal('balance', 10, 4)->default(0.00);
            $table->tinyInteger('override')->default(0);
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
        Schema::drop('loans');
    }
}
