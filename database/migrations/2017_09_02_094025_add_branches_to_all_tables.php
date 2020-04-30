<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBranchesToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrowers', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('assets', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('capital', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('emails', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('expenses', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('loans', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('loan_applications', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('loan_repayments', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('other_income', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('payroll', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('savings', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('sms', function ($table) {
            $table->integer('branch_id')->nullable();
        });
        Schema::table('audit_trail', function ($table) {
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
        Schema::table('borrowers', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('assets', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('capital', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('emails', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('expenses', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('loans', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('loan_applications', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('loan_repayments', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('other_income', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('payroll', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('savings', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('sms', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });
        Schema::table('audit_trail', function ($table) {
            $table->dropColumn([
                'branch_id'
            ]);
        });

    }
}
