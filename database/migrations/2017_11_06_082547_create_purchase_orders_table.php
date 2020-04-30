<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->decimal('amount', 65, 2)->default(0.00);
            $table->decimal('unit_cost', 65, 2)->default(0.00);
            $table->decimal('qty', 65, 2)->default(0.00);
            $table->decimal('total_cost', 65, 2)->default(0.00);
            $table->decimal('tax_rate',10, 2)->default(0.00);
            $table->decimal('tax_total', 10, 2)->default(0.00);
            $table->date('delivery_date')->nullable();
            $table->date('date')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
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
        Schema::drop('purchase_orders');
    }
}
