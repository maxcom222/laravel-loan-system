<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->text('name')->nullable();
            $table->integer('gl_code')->nullable();
            $table->enum('account_type',['asset','expense','equity','liability','income'])->default('asset');
            $table->tinyInteger('allow_manual')->default(0);
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
        Schema::drop('chart_of_accounts');
    }
}
