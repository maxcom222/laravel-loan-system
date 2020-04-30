<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollTemplateMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_template_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payroll_template_id')->unsigned();
            $table->string('name')->nullable();
            $table->enum('position',
                array('top_left', 'top_right', 'bottom_left', 'bottom_right'))->default('bottom_left');
            $table->tinyInteger('is_default')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payroll_template_meta');
    }
}
