<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDefaultBranchForCurrentLoans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $branch=\App\Models\Branch::first();
        //set borrowers
        foreach (\App\Models\Borrower::all() as $key){
            $b=\App\Models\Borrower::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\Loan::all() as $key){
            $b=\App\Models\Loan::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\Asset::all() as $key){
            $b=\App\Models\Asset::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\AuditTrail::all() as $key){
            $b=\App\Models\AuditTrail::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\Capital::all() as $key){
            $b=\App\Models\Capital::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\Email::all() as $key){
            $b=\App\Models\Email::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\Expense::all() as $key){
            $b=\App\Models\Expense::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\LoanRepayment::all() as $key){
            $b=\App\Models\LoanRepayment::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\LoanSchedule::all() as $key){
            $b=\App\Models\LoanSchedule::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\OtherIncome::all() as $key){
            $b=\App\Models\OtherIncome::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\Payroll::all() as $key){
            $b=\App\Models\Payroll::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\Saving::all() as $key){
            $b=\App\Models\Saving::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\SavingTransaction::all() as $key){
            $b=\App\Models\SavingTransaction::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
        foreach (\App\Models\Sms::all() as $key){
            $b=\App\Models\Sms::find($key->id);
            $b->branch_id=$branch->id;
            $b->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
