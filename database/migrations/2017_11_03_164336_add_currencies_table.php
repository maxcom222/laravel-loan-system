<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('rate')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('symbol')->nullable();
            $table->enum('position', ['left', 'right'])->default('left');
        });
        \Illuminate\Support\Facades\DB::table('currencies')->insert([
            [

                'rate' => '1.00',
                'code' => 'USD',
                'name' => 'United States dollar',
                'symbol' => '$',
                'position' => 'left',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('currencies');
    }
}
