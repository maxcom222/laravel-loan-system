<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsCheckOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_check_outs', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type',['cash','loan'])->default('cash');
            $table->integer('loan_id')->unsigned()->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('borrower_id')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->decimal('unit_cost', 65, 2)->default(0.00);
            $table->decimal('amount', 65, 2)->default(0.00);
            $table->decimal('qty', 65, 2)->default(0.00);
            $table->decimal('total_cost', 65, 2)->default(0.00);
            $table->decimal('tax_rate',10, 2)->default(0.00);
            $table->decimal('tax_total', 10, 2)->default(0.00);
            $table->date('date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
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
        Schema::drop('product_check_outs');
    }
}
