<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_order_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->decimal('tax_rate',65, 2)->default(0.00);
            $table->decimal('qty', 65, 2)->default(0.00);
            $table->decimal('qty_received', 65, 2)->default(0.00);
            $table->decimal('unit_cost', 65, 2)->default(0.00);
            $table->decimal('tax_total', 65, 2)->default(0.00);
            $table->decimal('total_cost', 65, 2)->default(0.00);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
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
        Schema::drop('purchase_order_items');
    }
}
