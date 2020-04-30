<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameToGuarantorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guarantor', function ($table) {
            $table->integer('branch_id')->unsigned()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['Male', 'Female'])->default('Male');
            $table->integer('country_id')->nullable();
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
            $table->text('files')->nullable();
        });
        //update guarantors table
        foreach (\App\Models\Guarantor::all() as $key){
            if(!empty(\App\Models\Borrower::find($key->guarantor_id))){
                $u=\App\Models\Borrower::find($key->guarantor_id);
                $borrower= \App\Models\LoanGuarantor::find($key->id);
                $borrower->first_name = $u->first_name;
                $borrower->last_name = $u->last_name;
                $borrower->gender = $u->gender;
                $borrower->country = $u->country;
                $borrower->title = $u->title;
                $borrower->mobile = $u->mobile;
                $borrower->notes = $u->notes;
                $borrower->email = $u->email;
                $borrower->photo = $u->photo;
                $borrower->unique_number = $u->unique_number;
                $borrower->dob = $u->dob;
                $borrower->address = $u->address;
                $borrower->city = $u->city;
                $borrower->state = $u->state;
                $borrower->zip = $u->zip;
                $borrower->phone = $u->phone;
                $borrower->business_name = $u->business_name;
                $borrower->working_status = $u->working_status;
                $borrower->files = $u->files;
                $borrower->save();
            }

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
