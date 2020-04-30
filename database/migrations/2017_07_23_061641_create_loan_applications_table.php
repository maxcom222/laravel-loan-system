<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('borrower_id')->unsigned()->nullable();
            $table->integer('loan_product_id');
            $table->decimal('amount', 10, 4)->default(0.00);
            $table->enum('status', array(
                'approved',
                'pending',
                'declined'
            ))->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
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
        Schema::drop('loan_applications');
    }
}
