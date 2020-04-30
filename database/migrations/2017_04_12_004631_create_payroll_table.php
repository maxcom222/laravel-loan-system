<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payroll_template_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('employee_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('description')->nullable();
            $table->text('comments')->nullable();
            $table->decimal('paid_amount',10,2)->default(0.00);
            $table->date('date')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->tinyInteger('recurring')->default(0);
            $table->string('recur_frequency')->default(31);
            $table->date('recur_start_date')->nullable();
            $table->date('recur_end_date')->nullable();
            $table->date('recur_next_date')->nullable();
            $table->enum('recur_type',
                array('day', 'week', 'month', 'year'))->default('month');
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
        Schema::drop('payroll');
    }
}
