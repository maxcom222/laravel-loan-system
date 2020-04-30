<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuarantorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guarantor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('loan_application_id')->nullable();
            $table->integer('loan_id')->nullable();
            $table->integer('borrower_id')->nullable();
            $table->integer('guarantor_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('saving_amount', 10, 2)->nullable();
            $table->decimal('accepted_amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'accepted','declined'])->default('pending')->nullable();
            $table->enum('saving_status', ['pending','hold', 'restored'])->default('pending')->nullable();
            $table->tinyInteger('saving_restored')->default(0);
            $table->text('notes')->nullable();
            $table->date('date')->nullable();
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
        Schema::drop('guarantor');
    }
}
