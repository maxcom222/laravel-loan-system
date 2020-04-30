<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBranchToSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_schedules', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('savings_transactions', function ($table) {
            $table->integer('branch_id')->nullable();
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
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('savings_transactions', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
    }
}
