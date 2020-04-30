<?php

namespace App\Listeners;

use App\Events\InterestWaived;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateInterestWaived
{

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InterestWaived $event
     * @return void
     */
    public function handle(InterestWaived $event)
    {
        $loan_transaction = $event->loan_transaction;
        foreach (LoanSchedule::where('loan_id', $loan_transaction->loan_id)->get() as $key) {
            $schedule = LoanSchedule::find($key->id);
            $schedule->interest_waived = 0;
            $schedule->save();

        }

        //get all  interest waived
        $excess_interest = 0;
        $covered_schedules = [];
        $covered_amount = 0;
        $next = true;
        $due_date = "";
        $count = 0;
        foreach (LoanTransaction::where('loan_id', $loan_transaction->loan_id)->where('transaction_type',
            'waiver')->where('reversed', 0)->orderBy('date', 'asc')->get() as $key) {
            $payments = LoanTransaction::where('loan_id', $loan_transaction->loan_id)->where('transaction_type',
                'repayment')->where('reversed', 0)->where('date', '<=',
                $key->date)->sum('credit');
            array_push($covered_schedules, $payments);
            $available = $key->credit;
            //loop through the schedules
            foreach (LoanSchedule::where('loan_id', $loan_transaction->loan_id)->get() as $skey) {
                $due = $skey->interest + $skey->principal + $skey->fees + $skey->penalty - $skey->interest_waived;
                if ($payments > 0) {
                    if ($payments > $due) {
                        $payments = $payments - $due;
                    } else {
                        //we now know that this is the last schedule where payment will apply
                        $repayment_order = unserialize($loan_transaction->loan->loan_product->repayment_order);
                        foreach ($repayment_order as $order) {
                            if ($order == 'interest') {
                                if ($payments > $skey->interest) {
                                    $payments = $payments - $skey->interest;
                                    //interest waived has to be moved to next schedules
                                    foreach (LoanSchedule::where('loan_id', $loan_transaction->loan_id)->where('due_date', '>',
                                        $skey->due_date)->orderBy('due_date', 'asc')->get() as $tkey) {
                                        if ($available > 0) {
                                            $interest_due = $tkey->interest - $tkey->interest_waived;
                                            if ($interest_due > 0) {
                                                if ($available > $interest_due) {
                                                    $available = $available - $interest_due;
                                                    $schedule = LoanSchedule::find($tkey->id);
                                                    $schedule->interest_waived = $schedule->interest_waived + $interest_due;
                                                    $schedule->save();

                                                } else {
                                                    $schedule = LoanSchedule::find($tkey->id);
                                                    $schedule->interest_waived = $schedule->interest_waived + $available;
                                                    $schedule->save();
                                                    $available = 0;
                                                    break;
                                                }
                                            }
                                        } else {
                                            break;
                                        }

                                    }
                                } else {
                                    //interest waived starts with this schedules
                                    $available=$available+$payments;
                                    if($available>($skey->interest-$payments-$skey->interest_waived)){
                                        if(($skey->interest-$payments-$skey->interest_waived)>0) {
                                            $schedule = LoanSchedule::find($skey->id);
                                            $schedule->interest_waived = $schedule->interest_waived + $skey->interest - $payments - $skey->interest_waived;
                                            $schedule->save();
                                            $available = $available - $skey->interest - $payments - $skey->interest_waived;
                                        }
                                        foreach (LoanSchedule::where('loan_id', $loan_transaction->loan_id)->where('due_date', '>',
                                            $skey->due_date)->orderBy('due_date', 'asc')->get() as $tkey) {
                                            if ($available > 0) {
                                                $interest_due = $tkey->interest - $tkey->interest_waived;
                                                if ($interest_due > 0) {
                                                    if ($available > $interest_due) {
                                                        $available = $available - $interest_due;
                                                        $schedule = LoanSchedule::find($tkey->id);
                                                        $schedule->interest_waived = $schedule->interest_waived + $interest_due;
                                                        $schedule->save();
                                                    } else {
                                                        $schedule = LoanSchedule::find($tkey->id);
                                                        $schedule->interest_waived = $schedule->interest_waived + $available;
                                                        $schedule->save();
                                                        $available = 0;
                                                        break;
                                                    }
                                                }
                                            } else {
                                                break;
                                            }

                                        }
                                    }


                                    $payments = 0;

                                }
                            }
                            if ($order == 'penalty') {
                                if ($payments > $skey->penalty) {
                                    $payments = $payments - $skey->penalty;
                                } else {
                                    $payments = 0;
                                }

                            }
                            if ($order == 'fees') {
                                if ($payments > $skey->fees) {
                                    $payments = $payments - $skey->fees;
                                } else {
                                    $payments = 0;
                                }

                            }
                            if ($order == 'principal') {
                                if ($payments > $skey->principal) {
                                    $payments = $payments - $skey->principal;
                                } else {
                                    $payments = 0;
                                }
                            }
                        }
                    }
                } else {
                    if($available>($skey->interest-$payments-$skey->interest_waived)){
                        if(($skey->interest-$payments-$skey->interest_waived)>0) {
                            $schedule = LoanSchedule::find($skey->id);
                            $schedule->interest_waived = $schedule->interest_waived + $skey->interest - $payments - $skey->interest_waived;
                            $schedule->save();
                            $available = $available - $skey->interest - $payments - $skey->interest_waived;
                        }
                        foreach (LoanSchedule::where('loan_id', $loan_transaction->loan_id)->where('due_date', '>',
                            $skey->due_date)->orderBy('due_date', 'asc')->get() as $tkey) {
                            if ($available > 0) {
                                $interest_due = $tkey->interest - $tkey->interest_waived;
                                if ($interest_due > 0) {
                                    if ($available > $interest_due) {
                                        $available = $available - $interest_due;
                                        $schedule = LoanSchedule::find($tkey->id);
                                        $schedule->interest_waived = $schedule->interest_waived + $interest_due;
                                        $schedule->save();
                                    } else {
                                        $schedule = LoanSchedule::find($tkey->id);
                                        $schedule->interest_waived = $schedule->interest_waived + $available;
                                        $schedule->save();
                                        $available = 0;
                                        break;
                                    }
                                }
                            } else {
                                break;
                            }

                        }
                    }
                }
            }
            // mail("tjmugova@webstudio.co.zw", "Test", $payments, "From: Test<test@test.com>");
           // break;
        }
        //mail("tjmugova@webstudio.co.zw", "Test", print_r($covered_schedules, true), "From: Test<test@test.com>");
    }


}
