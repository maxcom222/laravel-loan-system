<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id');
            $table->integer('borrower_id');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->decimal('principal', 10, 4)->nullable();
            $table->decimal('principal_balance', 10, 4)->nullable();
            $table->decimal('interest', 10, 4)->nullable();
            $table->decimal('fees', 10, 4)->nullable();
            $table->decimal('penalty', 10, 4)->nullable();
            $table->decimal('due', 10, 4)->nullable();
            $table->tinyInteger('system_generated')->default(0);
            $table->tinyInteger('closed')->default(0);
            $table->tinyInteger('missed')->default(0);
            $table->tinyInteger('missed_penalty_applied')->default(0);
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
        Schema::drop('loan_schedules');
    }
}
