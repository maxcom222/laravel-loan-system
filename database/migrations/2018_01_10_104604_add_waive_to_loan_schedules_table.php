<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWaiveToLoanSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_schedules', function ($table) {
            $table->decimal('fees_waived',65,4)->nullable();
            $table->decimal('penalty_waived',65,4)->nullable();
            $table->decimal('interest_waived',65,4)->nullable();
            $table->decimal('principal_waived',65,4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_schedules', function ($table) {
            $table->dropColumn('fees_waived');
            $table->dropColumn('penalty_waived');
            $table->dropColumn('interest_waived');
            $table->dropColumn('principal_waived');
        });
    }
}
