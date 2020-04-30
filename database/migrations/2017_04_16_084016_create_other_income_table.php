<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_income', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('other_income_type_id')->unsigned()->nullable();
            $table->decimal('amount',10,2)->default(0.00);
            $table->date('date')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->text('notes')->nullable();
            $table->text('files')->nullable();
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
        Schema::drop('other_income', function (Blueprint $table) {
            //
        });
    }
}
