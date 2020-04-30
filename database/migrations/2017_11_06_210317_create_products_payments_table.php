<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('bank_id')->unsigned()->nullable();
            $table->integer('product_check_in_id')->unsigned()->nullable();
            $table->integer('product_check_out_id')->unsigned()->nullable();
            $table->enum('type',['debit','credit'])->default('credit');
            $table->string('payment_method_id')->nullable();
            $table->text('receipt')->nullable();
            $table->text('payment_slip')->nullable();
            $table->decimal('amount', 65, 2);
            $table->text('notes')->nullable();
            $table->date('date');
            $table->string('month');
            $table->string('year');
            $table->timestamps();
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
        Schema::drop('product_payments');
    }
}
