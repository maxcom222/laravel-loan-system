<?php

/**
 * Created by PhpStorm.
 * User: Tj
 * Date: 6/29/2016
 * Time: 3:11 PM
 */

namespace App\Helpers;

use App\Models\Asset;
use App\Models\AssetValuation;
use App\Models\AuditTrail;
use App\Models\Capital;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\OtherIncome;
use App\Models\Payroll;
use App\Models\PayrollMeta;
use App\Models\Product;
use App\Models\ProductCheckin;
use App\Models\ProductCheckinItem;
use App\Models\ProductCheckout;
use App\Models\ProductCheckoutItem;
use App\Models\Saving;
use App\Models\SavingTransaction;
use App\Models\Setting;
use App\Models\SmsGateway;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Str;

class GeneralHelper
{
    //get active theme
    public static function get_active_theme_directory($sep = '.')
    {
        return 'themes' . $sep . Setting::where('setting_key', 'active_theme')->first()->setting_value;
    }

    /*
     * determine interest
     */
    public static function determine_interest_rate($id)
    {
        $loan = Loan::find($id);
        $interest = '';
        if ($loan->override_interest == 1) {
            $interest = $loan->override_interest_amount;
        } else {
            if ($loan->repayment_cycle == 'annually') {
                //return the interest per year
                if ($loan->interest_period == 'year') {
                    $interest = $loan->interest_rate;
                }
                if ($loan->interest_period == 'month') {
                    $interest = $loan->interest_rate * 12;
                }
                if ($loan->interest_period == 'week') {
                    $interest = $loan->interest_rate * 52;
                }
                if ($loan->interest_period == 'day') {
                    $interest = $loan->interest_rate * 365;
                }
            }
            if ($loan->repayment_cycle == 'semi_annually') {
                //return the interest per semi annually
                if ($loan->interest_period == 'year') {
                    $interest = $loan->interest_rate / 2;
                }
                if ($loan->interest_period == 'month') {
                    $interest = $loan->interest_rate * 6;
                }
                if ($loan->interest_period == 'week') {
                    $interest = $loan->interest_rate * 26;
                }
                if ($loan->interest_period == 'day') {
                    $interest = $loan->interest_rate * 182.5;
                }
            }
            if ($loan->repayment_cycle == 'quarterly') {
                //return the interest per quaterly

                if ($loan->interest_period == 'year') {
                    $interest = $loan->interest_rate / 4;
                }
                if ($loan->interest_period == 'month') {
                    $interest = $loan->interest_rate * 3;
                }
                if ($loan->interest_period == 'week') {
                    $interest = $loan->interest_rate * 13;
                }
                if ($loan->interest_period == 'day') {
                    $interest = $loan->interest_rate * 91.25;
                }
            }
            if ($loan->repayment_cycle == 'bi_monthly') {
                //return the interest per bi-monthly
                if ($loan->interest_period == 'year') {
                    $interest = $loan->interest_rate / 6;
                }
                if ($loan->interest_period == 'month') {
                    $interest = $loan->interest_rate * 2;
                }
                if ($loan->interest_period == 'week') {
                    $interest = $loan->interest_rate * 8.67;
                }
                if ($loan->interest_period == 'day') {
                    $interest = $loan->interest_rate * 58.67;
                }

            }

            if ($loan->repayment_cycle == 'monthly') {
                //return the interest per monthly

                if ($loan->interest_period == 'year') {
                    $interest = $loan->interest_rate / 12;
                }
                if ($loan->interest_period == 'month') {
                    $interest = $loan->interest_rate * 1;
                }
                if ($loan->interest_period == 'week') {
                    $interest = $loan->interest_rate * 4.33;
                }
                if ($loan->interest_period == 'day') {
                    $interest = $loan->interest_rate * 30.4;
                }
            }
            if ($loan->repayment_cycle == 'weekly') {
                //return the interest per weekly

                if ($loan->interest_period == 'year') {
                    $interest = $loan->interest_rate / 52;
                }
                if ($loan->interest_period == 'month') {
                    $interest = $loan->interest_rate / 4;
                }
                if ($loan->interest_period == 'week') {
                    $interest = $loan->interest_rate;
                }
                if ($loan->interest_period == 'day') {
                    $interest = $loan->interest_rate * 7;
                }
            }
            if ($loan->repayment_cycle == 'daily') {
                //return the interest per day

                if ($loan->interest_period == 'year') {
                    $interest = $loan->interest_rate / 365;
                }
                if ($loan->interest_period == 'month') {
                    $interest = $loan->interest_rate / 30.4;
                }
                if ($loan->interest_period == 'week') {
                    $interest = $loan->interest_rate / 7.02;
                }
                if ($loan->interest_period == 'day') {
                    $interest = $loan->interest_rate * 1;
                }
            }
        }
        return $interest / 100;
    }

//determine monthly payment using amortization
    public static function amortized_monthly_payment($id, $balance)
    {
        $loan = Loan::find($id);
        $period = GeneralHelper::loan_period($id);
        $interest_rate = GeneralHelper::determine_interest_rate($id);
        //calculate here
        $amount = ($interest_rate * $balance * pow((1 + $interest_rate), $period)) / (pow((1 + $interest_rate),
                    $period) - 1);
        return $amount;
    }

    public static function loan_period($id)
    {
        $loan = Loan::find($id);
        $period = 0;
        if ($loan->repayment_cycle == 'annually') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 12);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 52);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 365);
            }
        }
        if ($loan->repayment_cycle == 'semi_annually') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 2);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 6);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 26);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 182.5);
            }
        }
        if ($loan->repayment_cycle == 'quarterly') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 12);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 52);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 365);
            }
        }
        if ($loan->repayment_cycle == 'bi_monthly') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 6);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration / 2);

            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 8);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 60);
            }
        }

        if ($loan->repayment_cycle == 'monthly') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 12);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 4.3);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 30.4);
            }
        }
        if ($loan->repayment_cycle == 'weekly') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 52);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 4);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 1);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 7);
            }
        }
        if ($loan->repayment_cycle == 'daily') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 365);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 30.42);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 7.02);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration);
            }
        }
        return $period;
    }

    public static function time_ago($eventTime)
    {
        $totaldelay = time() - strtotime($eventTime);
        if ($totaldelay <= 0) {
            return '';
        } else {
            if ($days = floor($totaldelay / 86400)) {
                $totaldelay = $totaldelay % 86400;
                return $days . ' days ago';
            }
            if ($hours = floor($totaldelay / 3600)) {
                $totaldelay = $totaldelay % 3600;
                return $hours . ' hours ago';
            }
            if ($minutes = floor($totaldelay / 60)) {
                $totaldelay = $totaldelay % 60;
                return $minutes . ' minutes ago';
            }
            if ($seconds = floor($totaldelay / 1)) {
                $totaldelay = $totaldelay % 1;
                return $seconds . ' seconds ago';
            }
        }
    }

    public static function determine_due_date($id, $date)
    {
        $schedule = LoanSchedule::where('due_date', ' >=', $date)->where('loan_id', $id)->orderBy('due_date',
            'asc')->first();
        if (!empty($schedule)) {
            return $schedule->due_date;
        } else {
            $schedule = LoanSchedule::where('loan_id',
                $id)->orderBy('due_date',
                'desc')->first();
            if ($date > $schedule->due_date) {
                return $schedule->due_date;
            } else {
                $schedule = LoanSchedule::where('due_date', '>', $date)->where('loan_id',
                    $id)->orderBy('due_date',
                    'asc')->first();
                return $schedule->due_date;
            }

        }
    }

    public static function loan_total_interest($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('interest');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->sum('interest');
        }
    }

    public static function loan_total_interest_waived($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('interest_waived');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->sum('interest_waived');
        }
    }

    public static function loan_total_principal($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('principal');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->sum('principal');
        }
    }

    public static function loan_total_fees($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('fees');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=',
                $date)->sum('fees');
        }
    }

    public static function loan_total_penalty($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('penalty');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->sum('penalty');
        }
    }

    public static function loan_total_paid($id, $date = '')
    {
        if (empty($date)) {
            return LoanTransaction::where('loan_id', $id)->where('transaction_type',
                'repayment')->where('reversed', 0)->sum('credit');
        } else {
            return LoanTransaction::where('loan_id', $id)->where('transaction_type',
                'repayment')->where('reversed', 0)->where('due_date', '<=', $date)->sum('credit');
        }

    }

    public static function loan_total_balance($id, $date = '')
    {
        if (empty($date)) {
            return GeneralHelper::loan_total_due_amount($id) - GeneralHelper::loan_total_paid($id);
        } else {
            return GeneralHelper::loan_total_due_amount($id, $date) - GeneralHelper::loan_total_paid($id,
                    $date);
        }

    }

    public static function loan_total_due_amount($id, $date = '')
    {
        if (empty($date)) {
            return (GeneralHelper::loan_total_penalty($id) + GeneralHelper::loan_total_fees($id) + GeneralHelper::loan_total_interest($id) + GeneralHelper::loan_total_principal($id) - GeneralHelper::loan_total_interest_waived($id));
        } else {
            return (GeneralHelper::loan_total_penalty($id, $date) + GeneralHelper::loan_total_fees($id,
                    $date) + GeneralHelper::loan_total_interest($id, $date) + GeneralHelper::loan_total_principal($id,
                    $date) - GeneralHelper::loan_total_interest_waived($id, $date));

        }

    }

    public static function loan_total_due_period($id, $date)
    {
        return (LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('penalty') + LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('fees') + LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('principal') + LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('interest') + LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('interest_waived'));

    }

    public static function loan_total_paid_period($id, $date)
    {
        return LoanRepayment::where('loan_id', $id)->where('due_date', $date)->sum('amount');

    }

    public static function loans_total_paid($start_date = '', $end_date = '')
    {

        if (empty($start_date)) {
            $paid = 0;
            foreach (Loan::whereIn('status', ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $paid = $paid + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->sum('credit');
            }
            return $paid;
        } else {
            $paid = 0;
            foreach (Loan::whereIn('status', ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $paid = $paid + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->sum('credit');
            }
            return $paid;

        }

    }

    public static function diff_in_months(\DateTime $date1, \DateTime $date2)
    {
        $diff =  $date1->diff($date2);

        $months = $diff->y * 12 + $diff->m + $diff->d / 30;

        return (int) round($months);
    }
    public static function addMonths($date, $months)
    {
        $orig_day = $date->format("d");
        $date->modify("+" . $months . " months");
        while ($date->format("d") < $orig_day && $date->format("d") < 5) {
            $date->modify("-1 day");
        }
    }

    //determine paid principal
    public static function loan_paid_item($id, $item = 'principal', $date = '')
    {
        $loan = Loan::find($id);
        $principal = 0;
        $interest = 0;
        $penalty = 0;
        $fees = 0;
        if (empty($date)) {
            $schedules = $loan->schedules;
            $payments = LoanRepayment::where('loan_id', $id)->sum('amount');
        } else {
            $schedules = LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->get();
            $payments = LoanRepayment::where('loan_id', $id)->where('due_date', '<=', $date)->sum('amount');
        }

        if (!empty($loan->loan_product)) {
            $repayment_order = unserialize($loan->loan_product->repayment_order);
            foreach ($schedules as $schedule) {

                if ($payments > 0) {
                    foreach ($repayment_order as $order) {
                        if ($payments > 0) {
                            if ($order == 'interest') {
                                if ($payments > $schedule->interest) {
                                    $interest = $interest + $schedule->interest;
                                    $payments = $payments - $schedule->interest;
                                } else {
                                    $interest = $interest + $payments;
                                    $payments = 0;
                                }
                            }
                            if ($order == 'penalty') {
                                if ($payments > $schedule->penalty) {
                                    $penalty = $penalty + $schedule->penalty;
                                    $payments = $payments - $schedule->penalty;
                                } else {
                                    $penalty = $penalty + $payments;
                                    $payments = 0;
                                }
                            }
                            if ($order == 'fees') {
                                if ($payments > $schedule->fees) {
                                    $fees = $fees + $schedule->fees;
                                    $payments = $payments - $schedule->fees;
                                } else {
                                    $fees = $fees + $payments;
                                    $payments = 0;
                                }
                            }
                            if ($order == 'principal') {
                                if ($payments > $schedule->principal) {
                                    $principal = $principal + $schedule->principal;
                                    $payments = $payments - $schedule->principal;
                                } else {
                                    $principal = $principal + $payments;
                                    $payments = 0;
                                }
                            }
                        }
                    }
                }
                //apply remainder to principal
                $principal = $principal + $payments;
            }
        }
        if ($item == 'principal') {
            return $principal;
        }
        if ($item == 'fees') {
            return $fees;
        }
        if ($item == 'penalty') {
            return $penalty;
        }
        if ($item == 'interest') {
            return $interest;
        }
        return $principal;
    }

    public static function loan_terms_paid_item($id, $item = 'principal')
    {
        $loan = Loan::find($id);
        $principal = 0;
        $interest = 0;
        $penalty = 0;
        $fees = 0;
        $payments = GeneralHelper::loan_total_paid($id);
        $total_principal = GeneralHelper::loan_total_principal($id);
        $total_interest = GeneralHelper::loan_total_interest($id);
        $total_fees = GeneralHelper::loan_total_fees($id);
        $total_penalty = GeneralHelper::loan_total_penalty($id);
        if (!empty($loan->loan_product)) {
            $repayment_order = unserialize($loan->loan_product->repayment_order);
            if ($payments > 0) {
                foreach ($repayment_order as $order) {
                    if ($payments > 0) {
                        if ($order == 'interest') {
                            if ($payments > $total_interest) {
                                $interest = $interest + $total_interest;
                                $payments = $payments - $total_interest;
                            } else {
                                $interest = $interest + $payments;
                                $payments = 0;
                            }
                        }
                        if ($order == 'penalty') {
                            if ($payments > $total_penalty) {
                                $penalty = $penalty + $total_penalty;
                                $payments = $payments - $total_penalty;
                            } else {
                                $penalty = $penalty + $payments;
                                $payments = 0;
                            }
                        }
                        if ($order == 'fees') {
                            if ($payments > $total_fees) {
                                $fees = $fees + $total_fees;
                                $payments = $payments - $total_fees;
                            } else {
                                $fees = $fees + $payments;
                                $payments = 0;
                            }
                        }
                        if ($order == 'principal') {
                            if ($payments > $total_principal) {
                                $principal = $principal + $total_principal;
                                $payments = $payments - $total_principal;
                            } else {
                                $principal = $principal + $payments;
                                $payments = 0;
                            }
                        }

                    }
                }
                //apply remainder to principal
                $principal = $principal + $payments;
            }
        }
        if ($item == 'principal') {
            return $principal;
        }

        if ($item == 'fees') {
            return $fees;
        }
        if ($item == 'penalty') {
            return $penalty;
        }
        if ($item == 'interest') {
            return $interest;
        }
        return $principal;
    }


    public static function single_payroll_total_pay($id)
    {
        return PayrollMeta::where('payroll_id', $id)->where('position', 'bottom_left')->sum('value');
    }

    public static function single_payroll_total_deductions($id)
    {
        return PayrollMeta::where('payroll_id', $id)->where('position', 'bottom_right')->sum('value');
    }

    public static function total_expenses($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return Expense::where('branch_id', session('branch_id'))->sum('amount');
        } else {
            return Expense::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->sum('amount');

        }

    }

    public static function total_payroll($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $payroll = 0;
            foreach (Payroll::where('branch_id', session('branch_id'))->get() as $key) {
                $payroll = $payroll + GeneralHelper::single_payroll_total_pay($key->id);
            }
            return $payroll;
        } else {
            $payroll = 0;
            foreach (Payroll::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                $payroll = $payroll + GeneralHelper::single_payroll_total_pay($key->id);
            }
            return $payroll;

        }

    }

    public static function loans_total_principal($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $principal = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $principal = $principal + LoanSchedule::where('loan_id', $key->id)->sum('principal');
            }
            return $principal;
        } else {
            $principal = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $principal = $principal + $key->principal;
            }
            return $principal;

        }

    }

    public static function total_other_income($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return OtherIncome::where('branch_id', session('branch_id'))->sum('amount');
        } else {
            return OtherIncome::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->sum('amount');

        }

    }

    public static function total_savings_interest($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'interest')->where('reversed',0)->sum('debit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'interest')->where('reversed',0)->whereBetween('date',
                [$start_date, $end_date])->sum('debit');

        }

    }

    public static function total_savings_deposits($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type', 'deposit')->where('reversed',0)->sum('credit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'deposit')->where('reversed',0)->whereBetween('date',
                [$start_date, $end_date])->sum('credit');

        }

    }

    public static function total_savings_transactions($id, $start_date = '', $end_date = '')
    {
        $interest = 0;
        $deposits = 0;
        $withdrawals = 0;
        $fees = 0;
        $guarantee=0;
        $allocation = [];
        if (empty($start_date)) {
            foreach (SavingTransaction::where('savings_id', $id)->where('reversed',0)->get() as $key) {
                if ($key->type == "interest") {
                    $interest = $interest + $key->credit;
                }
                if ($key->type == "deposit") {
                    $deposits = $deposits + $key->credit;
                }
                if ($key->type == "interest") {
                    $withdrawals = $withdrawals + $key->debit;
                }
                if ($key->type == "bank_fees") {
                    $fees = $fees + $key->credit;
                }
                if ($key->type == "guarantee") {
                    $guarantee = $guarantee + $key->credit;
                }
            }

        } else {
            foreach (SavingTransaction::where('savings_id', $id)->where('reversed',0)->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                if ($key->type == "interest") {
                    $interest = $interest + $key->credit;
                }
                if ($key->type == "deposit") {
                    $deposits = $deposits + $key->credit;
                }
                if ($key->type == "interest") {
                    $withdrawals = $withdrawals + $key->debit;
                }
                if ($key->type == "bank_fees") {
                    $fees = $fees + $key->credit;
                }
                if ($key->type == "guarantee") {
                    $guarantee = $guarantee + $key->credit;
                }
            }

        }
        $allocation["interest"] = $interest;
        $allocation["deposits"] = $deposits;
        $allocation["withdrawals"] = $withdrawals;
        $allocation["fees"] = $fees;
        $allocation["guarantee"] = $guarantee;
        return $allocation;
    }

    public static function total_savings_withdrawals($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'withdrawal')->where('reversed',0)->sum('credit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'withdrawal')->where('reversed',0)->whereBetween('date',
                [$start_date, $end_date])->sum('credit');

        }

    }

    public static function total_capital($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return Capital::where('branch_id', session('branch_id'))->where('type',
                    'deposit')->sum('amount') - Capital::where('branch_id', session('branch_id'))->where('type',
                    'withdrawal')->sum('amount');
        } else {
            return Capital::where('branch_id', session('branch_id'))->where('type',
                    'deposit')->sum('amount') - Capital::where('branch_id', session('branch_id'))->where('type',
                    'withdrawal')->sum('amount');

        }

    }

    public static function loans_total_paid_item($item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }

    public static function loans_product_total_paid_item($id, $item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }

    public static function loans_borrower_total_paid_item($id, $item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }

    public static function loans_total_due_item($item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                if ($item == 'principal') {
                    $amount = $amount + GeneralHelper::loan_total_principal($key->id);
                }
                if ($item == 'interest') {
                    $amount = $amount + GeneralHelper::loan_total_interest($key->id);
                }
                if ($item == 'fees') {
                    $amount = $amount + GeneralHelper::loan_total_fees($key->id);
                }
                if ($item == 'penalty') {
                    $amount = $amount + GeneralHelper::loan_total_penalty($key->id);
                }

            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                if ($item == 'principal') {
                    $amount = $amount + GeneralHelper::loan_total_principal($key->id);
                }
                if ($item == 'interest') {
                    $amount = $amount + GeneralHelper::loan_total_interest($key->id);
                }
                if ($item == 'fees') {
                    $amount = $amount + GeneralHelper::loan_total_fees($key->id);
                }
                if ($item == 'penalty') {
                    $amount = $amount + GeneralHelper::loan_total_penalty($key->id);
                }
            }
            return $amount;
        }

    }

    public static function loans_product_total_due_items($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $interest = 0;
            $penalty = 0;
            $fees = 0;
            $principal = 0;
            foreach (Loan::where('loans.loan_product_id', $id)->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->where('loan_schedules.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;

            }
            return ["interest" => $interest, 'principal' => $principal, 'penalty' => $penalty, 'fees' => $fees];
        } else {
            $interest = 0;
            $penalty = 0;
            $fees = 0;
            $principal = 0;
            foreach (Loan::where('loans.loan_product_id', $id)->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->whereBetween('loan_schedules.due_date',
                [$start_date, $end_date])->where('loan_schedules.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;
            }
            return ["interest" => $interest, 'principal' => $principal, 'penalty' => $penalty, 'fees' => $fees];
        }

    }

    public static function loans_product_total_paid_items($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $interest = 0;
            $penalty = 0;
            $fees = 0;
            $principal = 0;
            foreach (Loan::where('loans.loan_product_id', $id)->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_repayments', 'loans.id', '=',
                'loan_repayments.loan_id')->where('loan_repayments.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;

            }
            return ["interest" => $interest, 'principal' => $principal, 'penalty' => $penalty, 'fees' => $fees];
        } else {
            $interest = 0;
            $penalty = 0;
            $fees = 0;
            $principal = 0;
            foreach (Loan::where('loans.loan_product_id', $id)->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_repayments', 'loans.id', '=',
                'loan_repayments.loan_id')->whereBetween('loan_repayments.collection_date',
                [$start_date, $end_date])->where('loan_repayments.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;
            }
            return ["interest" => $interest, 'principal' => $principal, 'penalty' => $penalty, 'fees' => $fees];
        }

    }

    public static function loans_borrower_total_due_item($id, $item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                if ($item == 'principal') {
                    $amount = $amount + GeneralHelper::loan_total_principal($key->id);
                }
                if ($item == 'interest') {
                    $amount = $amount + GeneralHelper::loan_total_interest($key->id);
                }
                if ($item == 'fees') {
                    $amount = $amount + GeneralHelper::loan_total_fees($key->id);
                }
                if ($item == 'penalty') {
                    $amount = $amount + GeneralHelper::loan_total_penalty($key->id);
                }

            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                if ($item == 'principal') {
                    $amount = $amount + GeneralHelper::loan_total_principal($key->id);
                }
                if ($item == 'interest') {
                    $amount = $amount + GeneralHelper::loan_total_interest($key->id);
                }
                if ($item == 'fees') {
                    $amount = $amount + GeneralHelper::loan_total_fees($key->id);
                }
                if ($item == 'penalty') {
                    $amount = $amount + GeneralHelper::loan_total_penalty($key->id);
                }
            }
            return $amount;
        }

    }

    public static function loans_total_default($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $principal = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->where('status', 'written_off')->get() as $key) {
                $principal = $principal + ($key->principal - GeneralHelper::loan_total_paid($key->id));
            }
            return $principal;
        } else {
            $principal = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->where('status',
                'written_off')->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $principal = $principal + ($key->principal - GeneralHelper::loan_total_paid($key->id));
            }
            return $principal;

        }

    }

    public static function loans_total_due($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $due = $due + GeneralHelper::loan_total_due_amount($key->id);
            }
            return $due;
        } else {
            $due = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $due = $due + GeneralHelper::loan_total_due_amount($key->id);
            }
            return $due;

        }
    }

    public static function loans_count($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            $due = $due + Loan::where('branch_id', session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->count();
            return $due;
        } else {
            $due = 0;
            $due = $due + Loan::where('branch_id', session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->count();
            return $due;

        }
    }

    public static function loans_product_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            $due = $due + Loan::where('loan_product_id', $id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->count();
            return $due;
        } else {
            $due = 0;
            $due = $due + Loan::where('loan_product_id', $id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->count();
            return $due;

        }
    }

    public static function loans_borrower_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            $due = $due + Loan::where('borrower_id', $id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->count();
            return $due;
        } else {
            $due = 0;
            $due = $due + Loan::where('borrower_id', $id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->count();
            return $due;

        }
    }

    public static function payments_product_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $due = $due + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->count();
            }
            return $due;
        } else {
            $due = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $due = $due + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->count();
            }
            return $due;

        }
    }

    public static function payments_borrower_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $due = $due + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->count();
            }
            return $due;
        } else {
            $due = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $due = $due + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->count();
            }
            return $due;

        }
    }

    public static function borrower_loans_total_due($id)
    {

        $due = 0;
        foreach (Loan::whereIn('status',
            ['disbursed', 'closed', 'written_off'])->where('borrower_id', $id)->get() as $key) {
            $due = $due + GeneralHelper::loan_total_due_amount($key->id);
        }
        return $due;

    }

    public static function borrower_loans_total_paid($id)
    {

        $paid = 0;
        foreach (Loan::whereIn('status',
            ['disbursed', 'closed', 'written_off'])->where('borrower_id', $id)->get() as $key) {
            $paid = $paid + LoanTransaction::where('loan_id',
                    $key->id)->where('transaction_type',
                    'repayment')->where('reversed', 0)->sum('credit');
        }
        return $paid;

    }

    public static function audit_trail($notes)
    {
        $audit_trail = new AuditTrail();
        $audit_trail->user_id = Sentinel::getUser()->id;
        $audit_trail->user = Sentinel::getUser()->first_name . ' ' . Sentinel::getUser()->last_name;
        $audit_trail->notes = $notes;
        $audit_trail->branch_id = session('branch_id');
        $audit_trail->save();

    }

    public static function savings_account_balance($id)
    {

        $balance = 0;
        $cr = 0;
        $dr = 0;
        foreach (SavingTransaction::where('savings_id', $id)->get() as $key) {
            $cr = $cr + $key->credit;
            $dr = $dr + $key->debit;
        }
        return $cr - $dr;

    }

    public static function borrower_savings_account_balance($id)
    {

        $balance = 0;
        foreach (Saving::where('borrower_id', $id)->get() as $key) {
            $balance = $balance - GeneralHelper::savings_account_balance($key->id);
        }
        return $balance;

    }

    public static function asset_valuation($id, $start_date = '')
    {

        if (empty($start_date)) {
            $value = 0;
            if (!empty(AssetValuation::where('asset_id', $id)->orderBy('date', 'desc')->first())) {
                $value = AssetValuation::where('asset_id', $id)->orderBy('date', 'desc')->first()->amount;
            }
            return $value;
        } else {
            $value = 0;
            if (!empty(AssetValuation::where('asset_id', $id)->where('date', '<=', $start_date)->orderBy('date',
                'desc')->first())
            ) {
                $value = AssetValuation::where('asset_id', $id)->where('date', '<=', $start_date)->orderBy('date',
                    'desc')->first()->amount;
            }
            return $value;

        }

    }

    public static function asset_type_valuation($id, $start_date = '')
    {

        if (empty($start_date)) {
            $value = 0;
            foreach (Asset::where('asset_type_id', $id)->get() as $key) {
                if (!empty(AssetValuation::where('asset_id', $key->id)->orderBy('date', 'desc')->first())) {
                    $value = AssetValuation::where('asset_id', $key->id)->orderBy('date', 'desc')->first()->amount;
                }
            }
            return $value;
        } else {
            $value = 0;
            foreach (Asset::where('asset_type_id', $id)->get() as $key) {
                if (!empty(AssetValuation::where('asset_id', $key->id)->where('date', '<=',
                    $start_date)->orderBy('date',
                    'desc')->first())
                ) {
                    $value = AssetValuation::where('asset_id', $key->id)->where('date', '<=',
                        $start_date)->orderBy('date',
                        'desc')->first()->amount;
                }
            }
            return $value;

        }

    }

    public static function bank_account_balance($id)
    {

        return Capital::where('bank_account_id', $id)->where('branch_id', session('branch_id'))->where('type',
                'deposit')->sum('amount') - Capital::where('bank_account_id', $id)->where('branch_id',
                session('branch_id'))->where('type',
                'withdrawal')->sum('amount');
    }

    public static function send_sms($to, $msg)
    {
        if (Setting::where('setting_key', 'sms_enabled')->first()->setting_value == 1) {
            if (!empty(SmsGateway::find(Setting::where('setting_key',
                'active_sms')->first()->setting_value))
            ) {
                $active_sms = SmsGateway::find(Setting::where('setting_key',
                    'active_sms')->first()->setting_value);
                $append = "&";
                $append .= $active_sms->to_name . "=" . $to;
                $append .= "&" . $active_sms->msg_name . "=" . $msg;
                $url = $active_sms->url . $append;
                //send sms here
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $curl_scraped_page = curl_exec($ch);
                curl_close($ch);
            }
        }

    }

    public static function buildTree($data, $parent = 0)
    {
        $tree = array();
        foreach ($data as $d) {
            if ($d['parent_id'] == $parent) {
                $children = GeneralHelper::buildTree($data, $d['id']);
                // set a trivial key
                if (!empty($children)) {
                    $d['_children'] = $children;
                }
                $tree[] = $d;
            }
        }
        return $tree;
    }

    public static function printTree($tree, $r = 0, $p = null)
    {
        foreach ($tree as $i => $t) {
            $dash = ($t['parent_id'] == 0) ? '' : str_repeat('-', $r) . ' ';
            printf("\t<option value='%d'>%s%s</option>\n", $t['id'], $dash, $t['name']);
            if (isset($t['_children'])) {
                GeneralHelper::printTree($t['_children'], $r + 1, $t['parent_id']);
            }
        }
    }

    public static function printTableTree($tree, $r = 0, $p = null)
    {
        $html = '';
        foreach ($tree as $i => $t) {
            $dash = ($t['parent_id'] == 0) ? '' : str_repeat('-', $r) . ' ';
            $html .= '<tr>';
            $html .= "<td>" . $dash . $t['name'] . '</td>';
            $html .= "<td>" . $t['slug'] . '</td>';
            if ($t['active'] == 1) {
                $html .= "<td><span class='label label-success'>" . trans_choice('general.yes', 1) . "</span></td>";
            } else {
                $html .= "<td><span class='label label-danger'>" . trans_choice('general.no', 1) . "</span></td>";
            }
            $html .= "<td>" . $t['notes'] . '</td>';
            $html .= "<td>" . count($t['products']) . '</td>';
            $html .= "<td> <div class='btn-group'>";
            $html .= '<button type="button" class="btn btn-info btn-xs dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">' . trans('general.choose');
            $html .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>';
            $html .= '<ul class="dropdown-menu" role="menu">';
            if (Sentinel::hasAccess('stock.update')) {
                $html .= '<li><a href="' . url('product/category/' . $t['id'] . '/edit') . '"><i
                                                        class="fa fa-edit"></i>' . trans('general.edit') . '</a>
                                        </li>';
            }
            if (Sentinel::hasAccess('stock.delete')) {
                $html .= '<li><a href="' . url('product/category/' . $t['id'] . '/delete') . '" class="delete"><i
                                                        class="fa fa-trash"></i>' . trans('general.delete') . '</a>
                                        </li>';
            }
            $html .= '</ul></div></td>';
            $html .= '</tr>';
            if (isset($t['_children'])) {
                $html .= GeneralHelper::printTableTree($t['_children'], $r + 1, $t['parent_id']);
            }
        }
        return $html;
    }

    public static function getUniqueSlug($model, $value)
    {
        $slug = Str::slug($value);
        $slugCount = count($model->whereRaw("slug REGEXP '^{$slug}(-[0-9]+)?$' and id != '{$model->id}'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }

    public static function limit_text($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

    public static function check_in_total_amount($id)
    {

        return ProductCheckinItem::where('product_check_in_id', $id)->sum('total_cost');
    }

    public static function check_in_total_paid_amount($id)
    {

        return ProductCheckinItem::where('product_check_in_id', $id)->sum('total_cost');
    }

    public static function check_out_total_amount($id)
    {

        return ProductCheckoutItem::where('product_check_out_id', $id)->sum('total_cost');
    }

    public static function check_ins_total_amount($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (ProductCheckin::where('branch_id', session('branch_id'))->get() as $key) {
                $due = $due + GeneralHelper::check_in_total_amount($key->id);
            }
            return $due;
        } else {
            $due = 0;
            foreach (ProductCheckin::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                $due = $due + GeneralHelper::check_in_total_amount($key->id);
            }
            return $due;

        }
    }

    public static function check_outs_total_amount($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (ProductCheckout::where('branch_id', session('branch_id'))->get() as $key) {
                $due = $due + GeneralHelper::check_in_total_amount($key->id);
            }
            return $due;
        } else {
            $due = 0;
            foreach (ProductCheckout::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                if ($key->type == 'cash') {
                    $due = $due + GeneralHelper::check_in_total_amount($key->id);
                } else {
                    if (!empty($key->loan)) {
                        $due = $due + GeneralHelper::loan_total_due_amount($key->loan_id);
                    }
                }

            }
            return $due;

        }
    }

    public static function stock_total_cost_amount()
    {
        $due = 0;
        foreach (Product::get() as $key) {
            $due = $due + ($key->qty * $key->cost_price);
        }
        return $due;
    }

    public static function stock_total_selling_amount()
    {
        $due = 0;
        foreach (Product::get() as $key) {
            $due = $due + ($key->qty * $key->selling_price);
        }
        return $due;
    }

    public static function loan_allocate_payment($loan_transaction)
    {

        $allocation = [];
        $loan = $loan_transaction->loan;
        $principal = 0;
        $fees = 0;
        $penalty = 0;
        $interest = 0;
        $amount = $loan_transaction->credit;
        if (!empty($loan->loan_product)) {
            //find all payments up to this date
            //subtract this current payment
            $payments = LoanTransaction::where('loan_id', $loan_transaction->loan_id)->where('transaction_type',
                'repayment')->where('reversed', 0)->where('date', '<=', $loan_transaction->date)->sum('credit')-$amount;
            foreach ($loan->schedules as $schedule) {
                if ($amount > 0) {
                    if (($schedule->principal + $schedule->fees + $schedule->penalty + $schedule->interest - $schedule->interest_waived) < $payments) {
                        $payments = $payments - ($schedule->principal + $schedule->fees + $schedule->penalty + $schedule->interest - $schedule->interest_waived);
                        //these schedules have been covered
                    } else {
                        //$schedules have not yet been covered
                        if ($payments > 0) {
                            //try to allocate the remaining payment to the respective elements
                            $repayment_order = unserialize($loan->loan_product->repayment_order);
                            foreach ($repayment_order as $order) {
                                if ($order == 'interest') {
                                    if ($payments > $schedule->interest - $schedule->interest_waived) {
                                        $schedule_interest = 0;
                                        $payments = $payments - $schedule->interest - $schedule->interest_waived;
                                    } else {
                                        $schedule_interest = $schedule->interest - $schedule->interest_waived - $payments;
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
                            if ((($schedule->principal + $schedule->fees + $schedule->penalty + $schedule->interest - $schedule->interest_waived)) == $amount) {
                                $principal = $principal + $schedule->principal;
                                $fees = $fees + $schedule->fees;
                                $penalty = $penalty + $schedule->penalty;
                                $interest = $interest + $schedule->interest;
                                $amount = 0;
                                break;
                            } else {
                                //check with loan product
                                $repayment_order = unserialize($loan->loan_product->repayment_order);
                                foreach ($repayment_order as $order) {
                                    if ($order == 'interest') {
                                        if ($amount > $schedule->interest - $schedule->interest_waived) {
                                            $interest = $interest + $schedule->interest - $schedule->interest_waived;
                                            $amount = $amount - $schedule->interest - $schedule->interest_waived;
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
        return $allocation;
    }

    public static function loan_schedule_dtermine_paid_by($id)
    {
        $schedule = LoanSchedule::find($id);
        $amount = $schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty;
        $payments = 0;
        foreach (LoanRepayment::where('loan_id', $schedule->loan_id)->orderBy('collection_date',
            'asc')->get() as $payment) {
            $payments = $payments + $payment->amount;
        }
    }

    public static function loan_paid_items($id, $start_date = '', $end_date = '')
    {
        $allocation = [];
        $loan = Loan::find($id);
        $principal = 0;
        $fees = 0;
        $penalty = 0;
        $interest_waived = 0;
        $interest = 0;

        if (!empty($loan->loan_product)) {
            if (empty($start_date)) {
                $payments = LoanTransaction::where('loan_id', $id)->where('transaction_type',
                    'repayment')->where('reversed', 0)->sum('credit');
                $interest_waived = LoanTransaction::where('loan_id', $id)->where('transaction_type',
                    'waiver')->where('reversed', 0)->sum('credit');
            } else {
                $payments = LoanTransaction::where('loan_id', $id)->where('transaction_type',
                    'repayment')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->sum('credit');
                $interest_waived = LoanTransaction::where('loan_id', $id)->where('transaction_type',
                    'waiver')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->sum('credit');
            }
            foreach ($loan->schedules as $schedule) {
                //$schedules have not yet been covered
                if ($payments > 0) {
                    //try to allocate the remaining payment to the respective elements
                    $repayment_order = unserialize($loan->loan_product->repayment_order);
                    foreach ($repayment_order as $order) {
                        if ($order == 'interest') {
                            if ($payments > ($schedule->interest - $schedule->interest_waived)) {
                                $interest = $interest + $schedule->interest - $schedule->interest_waived;
                                $payments = $payments - $schedule->interest - $schedule->interest_waived;
                            } else {
                                $interest = $interest + $payments;
                                $payments = 0;
                            }
                        }
                        if ($order == 'penalty') {
                            if ($payments > $schedule->penalty) {
                                $penalty = $penalty + $schedule->penalty;
                                $payments = $payments - $schedule->penalty;
                            } else {
                                $penalty = $penalty + $payments;
                                $payments = 0;
                            }
                        }
                        if ($order == 'fees') {
                            if ($payments > $schedule->fees) {
                                $fees = $fees + $schedule->fees;
                                $payments = $payments - $schedule->fees;
                            } else {

                                $fees = $fees + $payments;
                                $payments = 0;
                            }

                        }
                        if ($order == 'principal') {
                            if ($payments > $schedule->principal) {
                                $principal = $principal + $schedule->principal;
                                $payments = $payments - $schedule->principal;
                            } else {
                                $principal = $principal + $payments;
                                $payments = 0;
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
        $allocation["interest_waived"] = $interest_waived;
        $allocation["fees"] = $fees;
        $allocation["penalty"] = $penalty;
        return $allocation;
    }

    public static function loan_due_items($id, $start_date = '', $end_date = '')
    {
        $allocation = [];
        $principal = 0;
        $fees = 0;
        $penalty = 0;
        $interest = 0;
        if (empty($start_date)) {
            $schedules = LoanSchedule::where('loan_id', $id)->get();
        } else {
            $schedules = LoanSchedule::where('loan_id', $id)->whereBetween('due_date',
                [$start_date, $end_date])->get();
        }
        foreach ($schedules as $schedule) {
            $interest = $interest + $schedule->interest;
            $penalty = $penalty + $schedule->penalty;
            $fees = $fees + $schedule->fees;
            $principal = $principal + $schedule->principal;
        }
        $allocation["principal"] = $principal;
        $allocation["interest"] = $interest;
        $allocation["fees"] = $fees;
        $allocation["penalty"] = $penalty;
        return $allocation;
    }

    public static function schedule_due_amount($id)
    {
        $schedule = LoanSchedule::find($id);
        $amount = 0;
        $payments = LoanRepayment::where('loan_id', $schedule->loan_id)->sum('amount');
        foreach (LoanSchedule::where('due_date', '<=', $schedule->due_date)->where('loan_id',
            $schedule->loan_id)->get() as $key) {
            if ($key->id != $id) {
                $payments = $payments - ($key->interest + $key->penalty + $key->fees + $key->principal);
            }
        }
        if ($payments > 0 && $payments > ($schedule->interest + $schedule->penalty + $schedule->fees + $schedule->principal)) {
            $amount = 0;
        } elseif ($payments > 0 && $payments < ($schedule->interest + $schedule->penalty + $schedule->fees + $schedule->principal)) {
            $amount = $schedule->interest + $schedule->penalty + $schedule->fees + $schedule->principal - $payments;
        } else {
            $amount = $schedule->interest + $schedule->penalty + $schedule->fees + $schedule->principal;
        }
        return $amount;
    }

    public static function loans_paid_items($start_date = '', $end_date = '')
    {
        $allocation = [];
        $principal = 0;
        $fees = 0;
        $penalty = 0;
        $interest = 0;
        $interest_waived = 0;
        $over_payments = 0;
        if (empty($start_date)) {
            $principal = $principal + JournalEntry::where('transaction_type',
                    'repayment')->where('transaction_sub_type', 'repayment_principal')->where('reversed',
                    0)->where('branch_id', session('branch_id'))->sum('credit');
            $interest = $interest + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_interest')->where('reversed', 0)->where('branch_id',
                    session('branch_id'))->sum('credit');
            $fees = $fees + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_fees')->where('reversed', 0)->where('branch_id', session('branch_id'))->sum('credit');
            $penalty = $penalty + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_penalty')->where('reversed', 0)->where('branch_id', session('branch_id'))->sum('credit');
            $over_payments = $over_payments + JournalEntry::where('transaction_type',
                    'repayment')->where('transaction_sub_type',
                    'overpayment')->where('reversed', 0)->where('branch_id', session('branch_id'))->sum('credit');
        } else {

            $principal = $principal + JournalEntry::where('transaction_type',
                    'repayment')->where('transaction_sub_type', 'repayment_principal')->where('reversed',
                    0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
            $interest = $interest + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_interest')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
            $fees = $fees + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_fees')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
            $penalty = $penalty + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_penalty')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
            $over_payments = $over_payments + JournalEntry::where('transaction_type',
                    'repayment')->where('transaction_sub_type',
                    'overpayment')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
        }

        $allocation["principal"] = $principal;
        $allocation["interest"] = $interest;
        $allocation["fees"] = $fees;
        $allocation["penalty"] = $penalty;
        $allocation["over_payments"] = $over_payments;
        return $allocation;
    }

    public static function loans_due_items($start_date = '', $end_date = '')
    {
        $allocation = [];
        $principal = 0;
        $fees = 0;
        $penalty = 0;
        $interest = 0;

        if (empty($start_date)) {
            foreach (Loan::select("loan_schedules.principal", "loan_schedules.interest", "loan_schedules.penalty",
                "loan_schedules.fees")->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->where('loan_schedules.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;

            }

        } else {
            foreach (Loan::select("loan_schedules.principal", "loan_schedules.interest", "loan_schedules.penalty",
                "loan_schedules.fees")->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->whereBetween('loan_schedules.due_date',
                [$start_date, $end_date])->where('loan_schedules.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;
            }
        }

        $allocation["principal"] = $principal;
        $allocation["interest"] = $interest;
        $allocation["fees"] = $fees;
        $allocation["penalty"] = $penalty;
        return $allocation;
    }
}