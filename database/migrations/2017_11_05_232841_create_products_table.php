<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->string('brand_id')->nullable();
            $table->string('category_id')->nullable();
            $table->text('code')->nullable();
            $table->decimal('cost_price', 65, 2)->nullable();
            $table->decimal('selling_price', 65, 2)->nullable();
            $table->integer('qty')->unsigned()->default(0);
            $table->integer('alert_qty')->unsigned()->default(0);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->text('picture')->nullable();
            $table->text('slug')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
    }
}
