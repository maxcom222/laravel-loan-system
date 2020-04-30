<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollateralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collateral', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('loan_id')->unsigned()->nullable();
            $table->integer('borrower_id')->unsigned()->nullable();
            $table->integer('collateral_type_id')->unsigned()->nullable();
            $table->decimal('value',10,2)->default(0.00);
            $table->date('date')->nullable();
            $table->string('year')->nullable();
            $table->enum('status',array('returned_to_borrower','repossessed','repossession_initiated','sold','lost','collateral_with_borrower','deposited_into_branch'))->nullable();
            $table->string('month')->nullable();
            $table->text('notes')->nullable();
            $table->text('photo')->nullable();
            $table->text('files')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('model_name')->nullable();
            $table->string('model_number')->nullable();
            $table->date('manufacture_date')->nullable();
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
        Schema::drop('collateral');
    }
}
