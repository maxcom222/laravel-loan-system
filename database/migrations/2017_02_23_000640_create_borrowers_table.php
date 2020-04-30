<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBorrowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['Male', 'Female'])->default('Male');
            $table->string('country')->nullable();
            $table->string('title')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('unique_number')->nullable();
            $table->string('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('phone')->nullable();
            $table->string('business_name')->nullable();
            $table->string('working_status')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->text('files')->nullable();
            $table->text('loan_officers')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::drop('borrowers');
    }
}
