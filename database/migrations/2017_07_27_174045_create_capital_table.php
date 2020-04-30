<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capital', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
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
        Schema::drop('capital');
    }
}
