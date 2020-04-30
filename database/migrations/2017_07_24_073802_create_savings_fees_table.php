<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->text('savings_products')->nullable();
            $table->decimal('amount', 10, 2)->nullable()->default(0);
            $table->string('fees_posting')->nullable();
            $table->string('fees_adding')->nullable();
            $table->enum('new_fee_type', array('full', 'pro_rata'))->nullable()->default('full');
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
        Schema::drop('savings_fees');
    }
}
