<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanOverduePenaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_overdue_penalties', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name')->nullable();
            $table->enum('type', array('fixed', 'percentage'));
            $table->decimal('amount', 65, 2)->nullable();
            $table->integer('days')->default(10);
            $table->tinyInteger('active')->default(1);
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loan_overdue_penalties');
    }
}
