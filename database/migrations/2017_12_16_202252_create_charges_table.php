<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('name')->nullable();
            $table->enum('product', array('loan', 'savings'));
            $table->enum('charge_type',
                array(
                    'disbursement',
                    'specified_due_date',
                    'installment_fee',
                    'overdue_installment_fee',
                    'loan_rescheduling_fee',
                    'overdue_maturity',
                    'savings_activation',
                    'withdrawal_fee',
                    'annual_fee',
                    'monthly_fee'
                ));
            $table->enum('charge_option',
                array(
                    'fixed',
                    'percentage',
                    'principal_due',
                    'principal_interest',
                    'interest_due',
                    'total_due',
                    'original_principal'
                ));
            $table->tinyInteger('charge_frequency')->default(0);
            $table->enum('charge_frequency_type',
                array(
                    'days',
                    'weeks',
                    'months',
                    'years',
                ))->default('days');
            $table->integer('charge_frequency_amount')->default(0);
            $table->decimal('amount', 65, 2)->nullable();
            $table->enum('charge_payment_mode',
                array(
                    'regular',
                    'account_transfer',
                ))->default('regular');
            $table->integer('currency_id')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('penalty')->default(0);
            $table->tinyInteger('override')->default(0);
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
        Schema::drop('charges');
    }
}
