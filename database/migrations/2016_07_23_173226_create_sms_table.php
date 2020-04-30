<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms',function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->text('message')->nullable();
            $table->integer('recipients')->unsigned();
            $table->text('send_to')->nullable();
            $table->text('notes')->nullable();
            $table->string('gateway')->nullable();
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
        Schema::drop('sms');
    }
}
