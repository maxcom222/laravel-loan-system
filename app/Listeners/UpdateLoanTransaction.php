<?php

namespace App\Listeners;

use App\Models\JournalEntry;
use App\Models\LoanTransaction;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Events\LoanTransactionUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateLoanTransaction
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UpdateLoanTransaction $event
     * @return void
     */
    public function handle(LoanTransactionUpdated $event)
    {
        $loan_transaction = $event->loan_transaction;
        //gather payments before this date and for this transaction date only
        $payments = LoanTransaction::where('loan_id', $loan_transaction->loan_id)->where('transaction_type',
            'repayment')->where('reversed', 0)->where('date', '<=',
            $loan_transaction->date)->sum('credit');
        $original_payments = $payments;
        $loan = $loan_transaction->loan;
        foreach (LoanTransaction::where('loan_id', $loan_transaction->loan_id)->where('date', '>',
            $loan_transaction->date)->where('transaction_type',
            'repayment')->where('reversed', 0)->orderBy('date', 'asc')->get() as $key) {

            $allocation = [];
            $principal = 0;
            $fees = 0;
            $penalty = 0;
            $interest = 0;
            $amount = $key->credit;
            if (!empty($loan->loan_product)) {
                foreach ($loan->schedules as $schedule) {
                    if ($amount > 0) {
                        if (($schedule->principal + $schedule->fees + $schedule->penalty + $schedule->interest) < $payments) {
                            $payments = $payments - ($schedule->principal + $schedule->fees + $schedule->penalty + $schedule->interest - $schedule->interest_waived);
                            //these schedules have been covered
                        } else {
                            //$schedules have not yet been covered
                            if ($payments > 0) {
                                //try to allocate the remaining payment to the respective elements
                                $repayment_order = unserialize($loan->loan_product->repayment_order);
                                foreach ($repayment_order as $order) {
                                    if ($order == 'interest') {
                                        if ($payments > $schedule->interest) {
                                            $schedule_interest = 0;
                                            $payments = $payments - $schedule->interest;
                                        } else {
                                            $schedule_interest = $schedule->interest - $payments;
                                            $payments = 0;
                                            if ($amount > $schedule_interest) {
                                                $interest = $interest + $schedule_interest;
                                                $amount = $amount - $schedule_interest;
                                            } else {
                                                $interest = $interest + $amount;
                                                $amount = 0;
                                            }
                                        }
                                    }
                                    if ($order == 'penalty') {
                                        if ($payments > $schedule->penalty) {
                                            $schedule_penalty = 0;
                                            $payments = $payments - $schedule->penalty;
                                        } else {
                                            $schedule_penalty = $schedule->penalty - $payments;
                                            $payments = 0;
                                            if ($amount > $schedule_penalty) {
                                                $penalty = $penalty + $schedule_penalty;
                                                $amount = $amount - $schedule_penalty;
                                            } else {
                                                $penalty = $penalty + $amount;
                                                $amount = 0;
                                            }
                                        }

                                    }
                                    if ($order == 'fees') {
                                        if ($payments > $schedule->fees) {
                                            $payments = $payments - $schedule->fees;
                                            $schedule_fees = 0;
                                        } else {
                                            $schedule_fees = $schedule->fees - $payments;
                                            $payments = 0;
                                            if ($amount > $schedule_fees) {
                                                $fees = $fees + $schedule_fees;
                                                $amount = $amount - $schedule_fees;
                                            } else {
                                                $fees = $fees + $amount;
                                                $amount = 0;
                                            }
                                        }

                                    }
                                    if ($order == 'principal') {
                                        if ($payments > $schedule->principal) {
                                            $schedule_principal = 0;
                                            $payments = $payments - $schedule->principal;
                                        } else {
                                            $schedule_principal = $schedule->principal - $payments;
                                            $payments = 0;
                                            if ($amount > $schedule_principal) {
                                                $principal = $principal + $schedule_principal;
                                                $amount = $amount - $schedule_principal;
                                            } else {
                                                $principal = $principal + $amount;
                                                $amount = 0;
                                            }
                                        }

                                    }
                                }
                            } else {
                                if ((($schedule->principal + $schedule->fees + $schedule->penalty + $schedule->interest)) == $amount) {
                                    $principal = $principal + $schedule->principal;
                                    $fees = $fees + $schedule->interest;
                                    $penalty = $penalty + $schedule->fees;
                                    $interest = $interest + $schedule->penalty;
                                    $amount = 0;
                                    break;
                                } else {
                                    //check with loan product
                                    $repayment_order = unserialize($loan->loan_product->repayment_order);
                                    foreach ($repayment_order as $order) {
                                        if ($order == 'interest') {
                                            if ($amount > $schedule->interest) {
                                                $interest = $interest + $schedule->interest;
                                                $amount = $amount - $schedule->interest;
                                            } else {
                                                $interest = $interest + $amount;
                                                $amount = 0;
                                            }
                                        }
                                        if ($order == 'penalty') {
                                            if ($amount > $schedule->penalty) {
                                                $penalty = $penalty + $schedule->penalty;
                                                $amount = $amount - $schedule->penalty;
                                            } else {
                                                $penalty = $penalty + $amount;
                                                $amount = 0;
                                            }
                                        }
                                        if ($order == 'fees') {
                                            if ($amount > $schedule->fees) {
                                                $fees = $fees + $schedule->fees;
                                                $amount = $amount - $schedule->fees;
                                            } else {
                                                $fees = $fees + $amount;
                                                $amount = 0;
                                            }
                                        }
                                        if ($order == 'principal') {
                                            if ($amount > $schedule->principal) {
                                                $principal = $principal + $schedule->principal;
                                                $amount = $amount - $schedule->principal;
                                            } else {
                                                $principal = $principal + $amount;
                                                $amount = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        break;
                    }
                }
            }
            $allocation["principal"] = $principal;
            $allocation["interest"] = $interest;
            $allocation["fees"] = $fees;
            $allocation["penalty"] = $penalty;
            $original_payments = $original_payments + $key->credit;
            $payments = $original_payments;
            //update transactions
            $transaction = LoanTransaction::find($key->id);
            $transaction->reversible = 0;
            $transaction->reversed = 1;
            $transaction->reversal_type = "system";
            $transaction->debit = $transaction->credit;
            $transaction->save();
            //reverse journal transactions
            foreach (JournalEntry::where('reference', $key->id)->where('loan_id',
                $transaction->loan_id)->where('transaction_type', 'repayment')->get() as $ky) {
                $journal = JournalEntry::find($ky->id);
                if ($ky->debit > $ky->credit) {
                    $journal->credit = $journal->debit;
                } else {
                    $journal->debit = $journal->credit;
                }
                $journal->reversed = 1;
                $journal->save();
            }

            //enter new records
            //add interest transaction
            $new_transaction = new LoanTransaction();
            $new_transaction->user_id = Sentinel::getUser()->id;
            $new_transaction->branch_id = session('branch_id');
            $new_transaction->loan_id = $loan->id;
            $new_transaction->borrower_id = $loan->borrower_id;
            $new_transaction->transaction_type = "repayment";
            $new_transaction->receipt = $transaction->receipt;
            $new_transaction->date = $transaction->date;
            $new_transaction->reversible = 1;
            $new_transaction->repayment_method_id = $transaction->repayment_method_id;
            $date = explode('-', $transaction->date);
            $new_transaction->year = $date[0];
            $new_transaction->month = $date[1];
            $new_transaction->credit = $transaction->credit;
            $new_transaction->notes = $transaction->notes;
            $new_transaction->save();
            //fire payment added event
            //debit and credit the necessary accounts

            //return $allocation;
            //principal
            if ($allocation['principal'] > 0) {
                if (!empty($loan->loan_product->chart_loan_portfolio)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_principal';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $new_transaction->id;
                    $journal->credit = $allocation['principal'];
                    $journal->reference = $new_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_fund_source)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_fund_source->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $new_transaction->id;
                    $journal->debit = $allocation['principal'];
                    $journal->reference = $new_transaction->id;
                    $journal->save();
                }
            }
            //interest
            if ($allocation['interest'] > 0) {
                if (!empty($loan->loan_product->chart_income_interest)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_income_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_interest';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $new_transaction->id;
                    $journal->credit = $allocation['interest'];
                    $journal->reference = $new_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_interest)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $new_transaction->id;
                    $journal->debit = $allocation['interest'];
                    $journal->reference = $new_transaction->id;
                    $journal->save();
                }
            }
            //fees
            if ($allocation['fees'] > 0) {
                if (!empty($loan->loan_product->chart_income_fee)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_income_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_fees';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $new_transaction->id;
                    $journal->credit = $allocation['fees'];
                    $journal->reference = $new_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_fee)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_receivable_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $new_transaction->id;
                    $journal->debit = $allocation['fees'];
                    $journal->reference = $new_transaction->id;
                    $journal->save();
                }
            }
            if ($allocation['penalty'] > 0) {
                if (!empty($loan->loan_product->chart_income_penalty)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_income_penalty->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = $transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_penalty';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $new_transaction->id;
                    $journal->credit = $allocation['penalty'];
                    $journal->reference = $new_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_penalty)) {
                    $journal = new JournalEntry();
                    $journal->user_id = Sentinel::getUser()->id;
                    $journal->account_id = $loan->loan_product->chart_receivable_penalty->id;
                    $journal->date = $transaction->date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $new_transaction->id;
                    $journal->debit = $allocation['penalty'];
                    $journal->reference = $new_transaction->id;
                    $journal->save();
                }
            }

        }
    }
}
