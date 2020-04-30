<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Email;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanOverduePenalty;
use App\Models\LoanProductCharge;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\Payroll;
use App\Models\PayrollMeta;
use App\Models\PayrollTemplateMeta;
use App\Models\Saving;
use App\Models\SavingProduct;
use App\Models\SavingsCharge;
use App\Models\SavingsProductCharge;
use App\Models\SavingTransaction;
use App\Models\Setting;
use App\Models\Sms;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use PDF;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Http\Requests;

class CronController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Setting::where('setting_key', 'enable_cron')->first()->setting_value == 0) {
            //someone attempted to run con job but it is disabled
            Mail::raw('Someone attempted to run con job but it is disabled, please enable it in settings',
                function ($message) {
                    $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                        Setting::where('setting_key', 'company_name')->first()->setting_value);
                    $message->to(Setting::where('setting_key', 'company_email')->first()->setting_value);
                    $headers = $message->getHeaders();
                    $message->setContentType('text/html');
                    $message->setSubject('Cron Job Failed');

                });
            return 'cron job disabled';
        } else {
            //check for upcoming payments
            //send via email
            if (Setting::where('setting_key', 'auto_repayment_email_reminder')->first()->setting_value == 1) {
                $days = Setting::where('setting_key', 'auto_repayment_days')->first()->setting_value;
                $due_date = date_format(date_add(date_create(date("Y-m-d")),
                    date_interval_create_from_date_string($days . ' days')),
                    'Y-m-d');
                $schedules = LoanSchedule::where('due_date', $due_date)->get();
                foreach ($schedules as $schedule) {
                    //check if borrower has email
                    if (!empty($schedule->borrower->email)) {
                        $borrower = $schedule->borrower;
                        $loan = $schedule->loan;
                        $body = Setting::where('setting_key',
                            'loan_payment_reminder_email_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                        $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
                        $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                        $body = str_replace('{loanNumber}', $loan->id, $body);
                        $body = str_replace('{paymentAmount}',
                            round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty),
                                2), $body);
                        $body = str_replace('{paymentDate}', $schedule->due_date, $body);
                        $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
                        $body = str_replace('{loanDue}',
                            round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                        $body = str_replace('{loanBalance}',
                            round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                                2), $body);
                        Mail::raw($body, function ($message) use ($borrower, $loan) {
                            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                                Setting::where('setting_key', 'company_name')->first()->setting_value);
                            $message->to($borrower->email);
                            $headers = $message->getHeaders();
                            $message->setContentType('text/html');
                            $message->setSubject(Setting::where('setting_key',
                                'loan_payment_reminder_subject')->first()->setting_value);
                        });
                        $mail = new Email();
                        //$mail->user_id = Sentinel::getUser()->id;
                        $mail->message = $body;
                        $mail->subject = Setting::where('setting_key',
                            'loan_payment_reminder_subject')->first()->setting_value;
                        $mail->recipients = 1;
                        $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                        $mail->save();
                    }
                }
            }
            //send via sms
            if (Setting::where('setting_key', 'auto_repayment_sms_reminder')->first()->setting_value == 1) {
                $days = Setting::where('setting_key', 'auto_repayment_days')->first()->setting_value;
                $due_date = date_format(date_add(date_create(date("Y-m-d")),
                    date_interval_create_from_date_string($days . ' days')),
                    'Y-m-d');
                $schedules = LoanSchedule::where('due_date', $due_date)->get();
                foreach ($schedules as $schedule) {
                    //check if borrower has email
                    if (!empty($schedule->borrower->email)) {
                        $borrower = $schedule->borrower;
                        $loan = $schedule->loan;
                        $body = Setting::where('setting_key',
                            'loan_payment_reminder_email_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                        $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
                        $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                        $body = str_replace('{loanNumber}', $loan->id, $body);
                        $body = str_replace('{paymentAmount}',
                            round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty),
                                2), $body);
                        $body = str_replace('{paymentDate}', $schedule->due_date, $body);
                        $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
                        $body = str_replace('{loanDue}',
                            round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                        $body = str_replace('{loanBalance}',
                            round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                                2), $body);
                        if (!empty($borrower->mobile)) {
                            $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                            GeneralHelper::send_sms($borrower->mobile, $body);
                            $sms = new Sms();
                            //$sms->user_id = Sentinel::getUser()->id;
                            $sms->message = $body;
                            $sms->gateway = $active_sms;
                            $sms->recipients = 1;
                            $sms->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                            $sms->save();

                        }
                    }
                }
            }
            //check for missed repayments
            //send via email
            if (Setting::where('setting_key', 'auto_overdue_repayment_email_reminder')->first()->setting_value == 1) {
                $days = Setting::where('setting_key', 'auto_overdue_repayment_days')->first()->setting_value;
                $due_date = date_format(date_sub(date_create(date("Y-m-d")),
                    date_interval_create_from_date_string($days . ' days')),
                    'Y-m-d');
                $schedules = LoanSchedule::where('due_date', $due_date)->get();
                foreach ($schedules as $schedule) {
                    //check if borrower has email
                    if (!empty($schedule->borrower->email)) {
                        $borrower = $schedule->borrower;
                        $loan = $schedule->loan;
                        $payments = LoanRepayment::where('loan_id', $loan->id)->where('due_date',
                            $schedule->due_date)->sum('amount');
                        if ($payments == 0) {
                            $body = Setting::where('setting_key',
                                'missed_payment_email_template')->first()->setting_value;
                            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                            $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
                            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                            $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
                            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                            $body = str_replace('{loanNumber}', $loan->id, $body);
                            $body = str_replace('{paymentAmount}',
                                round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty),
                                    2), $body);
                            $body = str_replace('{paymentDate}', $schedule->due_date, $body);
                            $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
                            $body = str_replace('{loanDue}',
                                round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                            $body = str_replace('{loanBalance}',
                                round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                                    2), $body);
                            Mail::raw($body, function ($message) use ($borrower, $loan) {
                                $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                                    Setting::where('setting_key', 'company_name')->first()->setting_value);
                                $message->to($borrower->email);
                                $headers = $message->getHeaders();
                                $message->setContentType('text/html');
                                $message->setSubject(Setting::where('setting_key',
                                    'missed_payment_email_subject')->first()->setting_value);
                            });
                            $mail = new Email();
                            //$mail->user_id = Sentinel::getUser()->id;
                            $mail->message = $body;
                            $mail->subject = Setting::where('setting_key',
                                'missed_payment_email_subject')->first()->setting_value;
                            $mail->recipients = 1;
                            $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                            $mail->save();
                        } else {
                            //user has paid something
                        }
                    }
                }
            }
            //send via sms
            if (Setting::where('setting_key', 'auto_overdue_repayment_sms_reminder')->first()->setting_value == 1) {
                $days = Setting::where('setting_key', 'auto_overdue_repayment_days')->first()->setting_value;
                $due_date = date_format(date_sub(date_create(date("Y-m-d")),
                    date_interval_create_from_date_string($days . ' days')),
                    'Y-m-d');
                $schedules = LoanSchedule::where('due_date', $due_date)->get();
                foreach ($schedules as $schedule) {
                    //check if borrower has email
                    if (!empty($schedule->borrower->email)) {
                        $borrower = $schedule->borrower;
                        $loan = $schedule->loan;
                        $payments = LoanRepayment::where('loan_id', $loan->id)->where('due_date',
                            $schedule->due_date)->sum('amount');
                        if ($payments == 0) {
                            $body = Setting::where('setting_key',
                                'missed_payment_sms_template')->first()->setting_value;
                            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                            $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
                            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                            $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
                            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                            $body = str_replace('{loanNumber}', $loan->id, $body);
                            $body = str_replace('{paymentAmount}',
                                round(($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty),
                                    2), $body);
                            $body = str_replace('{paymentDate}', $schedule->due_date, $body);
                            $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
                            $body = str_replace('{loanDue}',
                                round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                            $body = str_replace('{loanBalance}',
                                round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                                    2), $body);
                            if (!empty($borrower->mobile)) {
                                $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                                GeneralHelper::send_sms($borrower->mobile, $body);
                                $sms = new Sms();
                                //$sms->user_id = Sentinel::getUser()->id;
                                $sms->message = $body;
                                $sms->gateway = $active_sms;
                                $sms->recipients = 1;
                                $sms->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                                $sms->save();
                            }
                        } else {
                            //user has paid something
                        }
                    }
                }
            }
            //check for overdue loans
            //send via email
            if (Setting::where('setting_key', 'auto_overdue_loan_email_reminder')->first()->setting_value == 1) {
                $days = Setting::where('setting_key', 'auto_overdue_loan_days')->first()->setting_value;
                $due_date = date_format(date_sub(date_create(date("Y-m-d")),
                    date_interval_create_from_date_string($days . ' days')),
                    'Y-m-d');
                $loans = Loan::where('maturity_date', $due_date)->where('loan_status', 'open')->get();
                foreach ($loans as $loan) {
                    //check if borrower has email
                    if (!empty($loan->borrower->email)) {
                        $borrower = $loan->borrower;

                        $body = Setting::where('setting_key',
                            'loan_overdue_email_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                        $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
                        $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                        $body = str_replace('{loanNumber}', $loan->id, $body);
                        $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
                        $body = str_replace('{loanDue}',
                            round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                        $body = str_replace('{loanBalance}',
                            round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                                2), $body);
                        Mail::raw($body, function ($message) use ($borrower, $loan) {
                            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                                Setting::where('setting_key', 'company_name')->first()->setting_value);
                            $message->to($borrower->email);
                            $headers = $message->getHeaders();
                            $message->setContentType('text/html');
                            $message->setSubject(Setting::where('setting_key',
                                'loan_overdue_email_subject')->first()->setting_value);
                        });
                        $mail = new Email();
                        //$mail->user_id = Sentinel::getUser()->id;
                        $mail->message = $body;
                        $mail->subject = Setting::where('setting_key',
                            'loan_overdue_email_subject')->first()->setting_value;
                        $mail->recipients = 1;
                        $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                        $mail->save();

                    }
                }
            }
            //send via sms
            if (Setting::where('setting_key', 'auto_overdue_loan_sms_reminder')->first()->setting_value == 1) {
                $days = Setting::where('setting_key', 'auto_overdue_loan_days')->first()->setting_value;
                $due_date = date_format(date_sub(date_create(date("Y-m-d")),
                    date_interval_create_from_date_string($days . ' days')),
                    'Y-m-d');
                $loans = Loan::where('maturity_date', $due_date)->where('loan_status', 'open')->get();
                foreach ($loans as $loan) {
                    //check if borrower has email
                    if (!empty($loan->borrower->email)) {
                        $borrower = $loan->borrower;
                        $body = Setting::where('setting_key',
                            'loan_overdue_sms_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                        $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
                        $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                        $body = str_replace('{loanNumber}', $loan->id, $body);
                        $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
                        $body = str_replace('{loanDue}',
                            round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                        $body = str_replace('{loanBalance}',
                            round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                                2), $body);
                        if (!empty($borrower->mobile)) {
                            $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                            GeneralHelper::send_sms($borrower->mobile, $body);
                            $sms = new Sms();
                            //$sms->user_id = Sentinel::getUser()->id;
                            $sms->message = $body;
                            $sms->gateway = $active_sms;
                            $sms->recipients = 1;
                            $sms->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                            $sms->save();
                        }
                    }
                }
            }
            //check for penalties
            //missed payment penalty
            $loans = Loan::where('status', 'disbursed')->get();
            foreach ($loans as $loan) {
                if (!empty($loan->loan_product)) {
                    if ($loan->loan_product->enable_late_repayment_penalty == 1) {
                        $schedules = LoanSchedule::where('loan_id', $loan->id)->where('missed_penalty_applied',
                            0)->orderBy('due_date', 'asc')->get();
                        foreach ($schedules as $schedule) {
                            if ($loan->loan_product->late_repayment_penalty_grace_period > 0) {
                                $date = date_format(date_add(date_create($schedule->due_date),
                                    date_interval_create_from_date_string($loan->loan_product->late_repayment_penalty_grace_period . ' days')),
                                    'Y-m-d');
                            } else {
                                $date = date("Y-m-d");
                            }
                            if ($date < date("Y-m-d")) {
                                if (GeneralHelper::loan_total_due_period($loan->id,
                                        $schedule->due_date) > GeneralHelper::loan_total_paid_period($loan->id,
                                        $schedule->due_date)
                                ) {
                                    $sch = LoanSchedule::find($schedule->id);
                                    $sch->missed_penalty_applied = 1;
                                    //determine which amount to use
                                    if ($loan->loan_product->late_repayment_penalty_type == "fixed") {
                                        $sch->penalty = $sch->penalty + $loan->loan_product->late_repayment_penalty_amount;
                                    } else {
                                        if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal') {
                                            $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'principal', $schedule->due_date));
                                            $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                        }
                                        if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal_interest') {
                                            $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                    $schedule->due_date) + GeneralHelper::loan_total_interest($loan->id,
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'principal',
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'interest', $schedule->due_date));
                                            $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                        }
                                        if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal_interest_fees') {
                                            $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                    $schedule->due_date) + GeneralHelper::loan_total_interest($loan->id,
                                                    $schedule->due_date) + GeneralHelper::loan_total_fees($loan->id,
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'principal',
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'interest',
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'fees',
                                                    $schedule->due_date));
                                            $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                        }
                                        if ($loan->loan_product->late_repayment_penalty_calculate == 'total_overdue') {
                                            $principal = (GeneralHelper::loan_total_due_amount($loan->id,
                                                    $schedule->due_date) - GeneralHelper::loan_total_paid($loan->id,
                                                    $schedule->due_date));
                                            $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                        }
                                    }
                                    $sch->save();
                                }
                            }
                        }
                    }
                }
            }
            //after maturity date payment
            $loans = Loan::where('status', 'disbursed')->get();
            foreach ($loans as $loan) {
                $product = $loan->loan_product;
                if (!empty($product)) {
                    //check for charges
                    foreach (LoanProductCharge::where('loan_product_id', $product->id)->where('penalty',
                        1)->get() as $tkey) {
                        if (!empty($tkey->charge)) {
                            $date = date_format(date_sub(date_create(date("Y-m-d")),
                                date_interval_create_from_date_string($product->late_repayment_penalty_grace_period . ' days')),
                                'Y-m-d');
                            //missed penalty charge
                            if ($tkey->charge->charge_type == "overdue_installment_fee") {
                                $schedule = LoanSchedule::where('loan_id', $loan->id)->where('due_date',
                                    $date)->first();
                                if (!empty($schedule)) {
                                    $due_items = GeneralHelper::loan_due_items($loan->id, $loan->release_date, $date);
                                    $paid_items = GeneralHelper::loan_paid_items($loan->id, $loan->release_date,
                                        date("Y-m-d"));

                                    if ($tkey->charge->charge_option == "fixed") {
                                        $amount = $tkey->charge->amount;
                                    }
                                    if ($tkey->charge->charge_option == "principal_due") {
                                        if (($due_items["principal"] - $paid_items["principal"]) > 0) {
                                            $amount = $tkey->charge->amount * (($due_items["principal"] - $paid_items["principal"]) / 100);
                                        } else {
                                            $amount = 0;
                                        }

                                    }
                                    if ($tkey->charge->charge_option == "principal_interest") {
                                        if (($due_items["principal"] + $due_items["interest"] - $paid_items["principal"] - $paid_items["interest"]) > 0) {
                                            $amount = $tkey->charge->amount * (($due_items["principal"] + $due_items["interest"] - $paid_items["principal"] - $paid_items["interest"]) / 100);
                                        } else {
                                            $amount = 0;
                                        }

                                    }
                                    if ($tkey->charge->charge_option == "interest_due") {
                                        if (($due_items["interest"] - $paid_items["interest"]) > 0) {
                                            $amount = $tkey->charge->amount * (($due_items["interest"] - $paid_items["interest"]) / 100);
                                        } else {
                                            $amount = 0;
                                        }

                                    }
                                    if ($tkey->charge->charge_option == "total_due") {
                                        if (($due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"] - $paid_items["principal"] - $paid_items["interest"] - $paid_items["fees"] - $paid_items["penalty"]) > 0) {
                                            $amount = $tkey->charge->amount * (($due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"] - $paid_items["principal"] - $paid_items["interest"] - $paid_items["fees"] - $paid_items["penalty"]) / 100);
                                        } else {
                                            $amount = 0;
                                        }

                                    }
                                    if ($tkey->charge->charge_option == "original_principal") {
                                        $amount = $tkey->charge->amount * $loan->principal / 100;

                                    }
                                    if ($amount > 0) {
                                        $loan_transaction = new LoanTransaction();
                                        $loan_transaction->branch_id = $loan->branch_id;
                                        $loan_transaction->loan_id = $loan->id;
                                        $loan_transaction->borrower_id = $loan->borrower_id;
                                        $loan_transaction->transaction_type = "overdue_installment_fee";
                                        $loan_transaction->date = $due_date;
                                        $date = explode('-', $due_date);
                                        $loan_transaction->year = $date[0];
                                        $loan_transaction->month = $date[1];
                                        $loan_transaction->debit = $amount;
                                        $loan_transaction->reversible = 1;
                                        $loan_transaction->save();
                                        //update schedule
                                        $schedule->penalty = $schedule->penalty + $amount;
                                        $schedule->missed_penalty_applied = 1;
                                        $schedule->save();
                                    }
                                }
                            }
                            //overdue penalty
                            if ($tkey->charge->charge_type == "overdue_maturity") {
                                $due_items = GeneralHelper::loan_due_items($loan->id, $loan->release_date,
                                    date("Y-m-d"));
                                $paid_items = GeneralHelper::loan_paid_items($loan->id, $loan->release_date,
                                    date("Y-m-d"));
                                if ($loan->maturity_date < date("Y-m-d") && ($due_items["interest"] + $due_items["principal"] + $due_items["fees"] + $due_items["penalty"] - $paid_items["interest"] - $paid_items["principal"] - $paid_items["fees"] - $paid_items["penalty"]) > 0) {
                                    if ($tkey->charge->charge_option == "fixed") {
                                        $amount = $tkey->charge->amount;
                                    }
                                    if ($tkey->charge->charge_option == "principal_due") {
                                        if (($due_items["principal"] - $paid_items["principal"]) > 0) {
                                            $amount = $tkey->charge->amount * (($due_items["principal"] - $paid_items["principal"]) / 100);
                                        } else {
                                            $amount = 0;
                                        }

                                    }
                                    if ($tkey->charge->charge_option == "principal_interest") {
                                        if (($due_items["principal"] + $due_items["interest"] - $paid_items["principal"] - $paid_items["interest"]) > 0) {
                                            $amount = $tkey->charge->amount * (($due_items["principal"] + $due_items["interest"] - $paid_items["principal"] - $paid_items["interest"]) / 100);
                                        } else {
                                            $amount = 0;
                                        }

                                    }
                                    if ($tkey->charge->charge_option == "interest_due") {
                                        if (($due_items["interest"] - $paid_items["interest"]) > 0) {
                                            $amount = $tkey->charge->amount * (($due_items["interest"] - $paid_items["interest"]) / 100);
                                        } else {
                                            $amount = 0;
                                        }

                                    }
                                    if ($tkey->charge->charge_option == "total_due") {
                                        if (($due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"] - $paid_items["principal"] - $paid_items["interest"] - $paid_items["fees"] - $paid_items["penalty"]) > 0) {
                                            $amount = $tkey->charge->amount * (($due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"] - $paid_items["principal"] - $paid_items["interest"] - $paid_items["fees"] - $paid_items["penalty"]) / 100);
                                        } else {
                                            $amount = 0;
                                        }

                                    }
                                    if ($tkey->charge->charge_option == "original_principal") {
                                        $amount = $tkey->charge->amount * $loan->principal / 100;

                                    }
                                    $schedule = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date',
                                        'desc')->first();
                                    if (!empty($schedule)) {
                                        if ($amount > 0) {
                                            $loan_transaction = new LoanTransaction();
                                            $loan_transaction->branch_id = $loan->branch_id;
                                            $loan_transaction->loan_id = $loan->id;
                                            $loan_transaction->borrower_id = $loan->borrower_id;
                                            $loan_transaction->transaction_type = "overdue_maturity";
                                            $loan_transaction->date = date("Y-m-d");
                                            $date = explode('-', date("Y-m-d"));
                                            $loan_transaction->year = $date[0];
                                            $loan_transaction->month = $date[1];
                                            $loan_transaction->debit = $amount;
                                            $loan_transaction->reversible = 1;
                                            $loan_transaction->save();
                                            //update schedule
                                            $schedule->penalty = $schedule->penalty + $amount;
                                            $schedule->missed_penalty_applied = 1;
                                            $schedule->save();
                                        }
                                    }
                                }

                            }

                        }
                    }

                }
            }
            //check for recurring expenses
            $expenses = Expense::where('recurring', 1)->get();
            foreach ($expenses as $expense) {
                if (empty($expense->recur_end_date)) {
                    if ($expense->recur_next_date == date("Y-m-d")) {
                        $exp1 = new Expense();
                        $exp1->expense_type_id = $expense->expense_type_id;
                        $exp1->amount = $expense->amount;
                        $exp1->notes = $expense->notes;
                        $exp1->date = date("Y-m-d");
                        $date = explode('-', date("Y-m-d"));
                        $exp1->year = $date[0];
                        $exp1->month = $date[1];
                        $exp1->save();
                        $custom_fields = CustomFieldMeta::where('parent_id', $expense->id)->where('category',
                            'expenses')->get();
                        foreach ($custom_fields as $key) {
                            $custom_field = new CustomFieldMeta();
                            $custom_field->name = $key->name;
                            $custom_field->parent_id = $exp1->id;
                            $custom_field->custom_field_id = $key->custom_field_id;
                            $custom_field->category = "expenses";
                            $custom_field->save();
                        }
                        $exp2 = Expense::find($expense->id);
                        $exp2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
                            date_interval_create_from_date_string($expense->recur_frequency . ' ' . $expense->recur_type . 's')),
                            'Y-m-d');
                        $exp2->save();
                    }
                } else {
                    if (date("Y-m-d") <= $expense->recur_end_date) {
                        if ($expense->recur_next_date == date("Y-m-d")) {
                            $exp1 = new Expense();
                            $exp1->expense_type_id = $expense->expense_type_id;
                            $exp1->amount = $expense->amount;
                            $exp1->notes = $expense->notes;
                            $exp1->date = date("Y-m-d");
                            $date = explode('-', date("Y-m-d"));
                            $exp1->year = $date[0];
                            $exp1->month = $date[1];
                            $exp1->save();
                            $custom_fields = CustomFieldMeta::where('parent_id', $expense->id)->where('category',
                                'expenses')->get();
                            foreach ($custom_fields as $key) {
                                $custom_field = new CustomFieldMeta();
                                $custom_field->name = $key->name;
                                $custom_field->parent_id = $exp1->id;
                                $custom_field->custom_field_id = $key->custom_field_id;
                                $custom_field->category = "expenses";
                                $custom_field->save();
                            }
                            $exp2 = Expense::find($expense->id);
                            $exp2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
                                date_interval_create_from_date_string($expense->recur_frequency . ' ' . $expense->recur_type . 's')),
                                'Y-m-d');
                            $exp2->save();
                        }
                    }
                }
            }
            //check for recurring payroll
            $payrolls = Payroll::where('recurring', 1)->get();
            foreach ($payrolls as $payroll) {
                if (empty($payroll->recur_end_date)) {
                    if ($payroll->recur_next_date == date("Y-m-d")) {
                        $pay1 = new Payroll();
                        $pay1->payroll_template_id = $payroll->payroll_template_id;
                        $pay1->user_id = $payroll->user_id;
                        $pay1->employee_name = $payroll->employee_name;
                        $pay1->business_name = $payroll->business_name;
                        $pay1->payment_method = $payroll->payment_method;
                        $pay1->bank_name = $payroll->bank_name;
                        $pay1->account_number = $payroll->account_number;
                        $pay1->description = $payroll->description;
                        $pay1->comments = $payroll->comments;
                        $pay1->paid_amount = $payroll->paid_amount;
                        $date = explode('-', date("Y-m-d"));
                        $pay1->date = date("Y-m-d");
                        $pay1->year = $date[0];
                        $pay1->month = $date[1];
                        $pay1->save();
                        //save payroll meta
                        $metas = PayrollMeta::where('payroll_id',
                            $payroll->id)->get();;
                        foreach ($metas as $key) {
                            $meta = new PayrollMeta();
                            $meta->value = $key->value;
                            $meta->payroll_id = $pay1->id;
                            $meta->payroll_template_meta_id = $key->payroll_template_meta_id;
                            $meta->position = $key->position;
                            $meta->save();
                        }
                        $pay2 = Payroll::find($payroll->id);
                        $pay2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
                            date_interval_create_from_date_string($payroll->recur_frequency . ' ' . $payroll->recur_type . 's')),
                            'Y-m-d');
                        $pay2->save();
                    } else {
                        if (date("Y-m-d") <= $payroll->recur_end_date) {
                            if ($payroll->recur_next_date == date("Y-m-d")) {
                                $pay1 = new Payroll();
                                $pay1->payroll_template_id = $payroll->payroll_template_id;
                                $pay1->user_id = $payroll->user_id;
                                $pay1->employee_name = $payroll->employee_name;
                                $pay1->business_name = $payroll->business_name;
                                $pay1->payment_method = $payroll->payment_method;
                                $pay1->bank_name = $payroll->bank_name;
                                $pay1->account_number = $payroll->account_number;
                                $pay1->description = $payroll->description;
                                $pay1->comments = $payroll->comments;
                                $pay1->paid_amount = $payroll->paid_amount;
                                $date = explode('-', date("Y-m-d"));
                                $pay1->date = date("Y-m-d");
                                $pay1->year = $date[0];
                                $pay1->month = $date[1];
                                $pay1->save();
                                //save payroll meta
                                $metas = PayrollMeta::where('payroll_id',
                                    $payroll->id)->get();;
                                foreach ($metas as $key) {
                                    $meta = new PayrollMeta();
                                    $meta->value = $key->value;
                                    $meta->payroll_id = $pay1->id;
                                    $meta->payroll_template_meta_id = $key->payroll_template_meta_id;
                                    $meta->position = $key->position;
                                    $meta->save();
                                }
                                $pay2 = Payroll::find($payroll->id);
                                $pay2->recur_next_date = date_format(date_add(date_create(date("Y-m-d")),
                                    date_interval_create_from_date_string($payroll->recur_frequency . ' ' . $payroll->recur_type . 's')),
                                    'Y-m-d');
                                $pay2->save();
                            }
                        }
                    }
                }
            }
            //savings interest and charges
            foreach (SavingProduct::all() as $key) {
                //check post date
                foreach (Saving::where('savings_product_id', $key->id)->where('status',
                    'active')->get() as $saving) {

                    //check for fees
                    foreach (SavingsProductCharge::where('savings_product_id', $key->id)->get() as $tkey) {
                        if (!empty($tkey->charge)) {
                            //specified due date charge
                            if ($tkey->charge->charge_type == "specified_due_date") {
                                //check if the date is today
                                foreach (SavingsCharge::where('savings_id', $saving->id)->where('charge_id',
                                    $tkey->charge_id)->where('date', date("Y-m-d"))->get() as $charge) {
                                    $savings_transaction = new SavingTransaction();
                                    $savings_transaction->borrower_id = $saving->borrower_id;
                                    $savings_transaction->branch_id = $saving->branch_id;
                                    $savings_transaction->savings_id = $saving->id;
                                    $savings_transaction->type = "bank_fees";
                                    $savings_transaction->reversible = 1;
                                    $savings_transaction->date = date("Y-m-d");
                                    $savings_transaction->time = date("H:i");
                                    $date = explode('-', date("Y-m-d"));
                                    $savings_transaction->year = $date[0];
                                    $savings_transaction->month = $date[1];
                                    $savings_transaction->debit = $tkey->charge->amount;
                                    $savings_transaction->save();
                                    if (!empty($key->chart_reference)) {
                                        $journal = new JournalEntry();
                                        $journal->account_id = $key->chart_reference->id;
                                        $journal->branch_id = $savings_transaction->branch_id;
                                        $journal->date = date("Y-m-d");
                                        $journal->year = $date[0];
                                        $journal->month = $date[1];
                                        $journal->borrower_id = $savings_transaction->borrower_id;
                                        $journal->transaction_type = 'pay_charge';
                                        $journal->name = "Charge";
                                        $journal->savings_id = $saving->id;
                                        $journal->credit = $tkey->charge->amount;
                                        $journal->reference = $savings_transaction->id;
                                        $journal->save();
                                    }
                                    if (!empty($key->chart_control)) {
                                        $journal = new JournalEntry();
                                        $journal->account_id = $key->chart_control->id;
                                        $journal->branch_id = $savings_transaction->branch_id;
                                        $journal->date = date("Y-m-d");
                                        $journal->year = $date[0];
                                        $journal->month = $date[1];
                                        $journal->borrower_id = $savings_transaction->borrower_id;
                                        $journal->transaction_type = 'pay_charge';
                                        $journal->name = "Charge";
                                        $journal->savings_id = $saving->id;
                                        $journal->debit = $tkey->charge->amount;
                                        $journal->reference = $savings_transaction->id;
                                        $journal->save();
                                    }
                                }
                            }
                            //monthly fee
                            if ($tkey->charge->charge_type == "monthly_fee" && date("d") == "01") {
                                $savings_transaction = new SavingTransaction();
                                $savings_transaction->borrower_id = $saving->borrower_id;
                                $savings_transaction->branch_id = $saving->branch_id;
                                $savings_transaction->savings_id = $saving->id;
                                $savings_transaction->type = "bank_fees";
                                $savings_transaction->reversible = 1;
                                $savings_transaction->date = date("Y-m-d");
                                $savings_transaction->time = date("H:i");
                                $date = explode('-', date("Y-m-d"));
                                $savings_transaction->year = $date[0];
                                $savings_transaction->month = $date[1];
                                $savings_transaction->debit = $tkey->charge->amount;
                                $savings_transaction->save();
                                if (!empty($key->chart_reference)) {
                                    $journal = new JournalEntry();
                                    $journal->account_id = $key->chart_reference->id;
                                    $journal->branch_id = $savings_transaction->branch_id;
                                    $journal->date = date("Y-m-d");
                                    $journal->year = $date[0];
                                    $journal->month = $date[1];
                                    $journal->borrower_id = $savings_transaction->borrower_id;
                                    $journal->transaction_type = 'pay_charge';
                                    $journal->name = "Charge";
                                    $journal->savings_id = $saving->id;
                                    $journal->credit = $tkey->charge->amount;
                                    $journal->reference = $savings_transaction->id;
                                    $journal->save();
                                }
                                if (!empty($key->chart_control)) {
                                    $journal = new JournalEntry();
                                    $journal->account_id = $key->chart_control->id;
                                    $journal->branch_id = $savings_transaction->branch_id;
                                    $journal->date = date("Y-m-d");
                                    $journal->year = $date[0];
                                    $journal->month = $date[1];
                                    $journal->borrower_id = $savings_transaction->borrower_id;
                                    $journal->transaction_type = 'pay_charge';
                                    $journal->name = "Charge";
                                    $journal->savings_id = $saving->id;
                                    $journal->debit = $tkey->charge->amount;
                                    $journal->reference = $savings_transaction->id;
                                    $journal->save();
                                }
                            }
                            if ($tkey->charge->charge_type == "annual_fee" && date("d-m") == "01-01") {
                                $savings_transaction = new SavingTransaction();
                                $savings_transaction->borrower_id = $saving->borrower_id;
                                $savings_transaction->branch_id = $saving->branch_id;
                                $savings_transaction->savings_id = $saving->id;
                                $savings_transaction->type = "bank_fees";
                                $savings_transaction->reversible = 1;
                                $savings_transaction->date = date("Y-m-d");
                                $savings_transaction->time = date("H:i");
                                $date = explode('-', date("Y-m-d"));
                                $savings_transaction->year = $date[0];
                                $savings_transaction->month = $date[1];
                                $savings_transaction->debit = $tkey->charge->amount;
                                $savings_transaction->save();
                                if (!empty($key->chart_reference)) {
                                    $journal = new JournalEntry();
                                    $journal->account_id = $key->chart_reference->id;
                                    $journal->branch_id = $savings_transaction->branch_id;
                                    $journal->date = date("Y-m-d");
                                    $journal->year = $date[0];
                                    $journal->month = $date[1];
                                    $journal->borrower_id = $savings_transaction->borrower_id;
                                    $journal->transaction_type = 'pay_charge';
                                    $journal->name = "Charge";
                                    $journal->savings_id = $saving->id;
                                    $journal->credit = $tkey->charge->amount;
                                    $journal->reference = $savings_transaction->id;
                                    $journal->save();
                                }
                                if (!empty($key->chart_control)) {
                                    $journal = new JournalEntry();
                                    $journal->account_id = $key->chart_control->id;
                                    $journal->branch_id = $savings_transaction->branch_id;
                                    $journal->date = date("Y-m-d");
                                    $journal->year = $date[0];
                                    $journal->month = $date[1];
                                    $journal->borrower_id = $savings_transaction->borrower_id;
                                    $journal->transaction_type = 'pay_charge';
                                    $journal->name = "Charge";
                                    $journal->savings_id = $saving->id;
                                    $journal->debit = $tkey->charge->amount;
                                    $journal->reference = $savings_transaction->id;
                                    $journal->save();
                                }
                            }
                        }
                    }
                    //apply interest
                    if (GeneralHelper::savings_account_balance($saving->id) >= $key->minimum_balance && $key->interest_rate > 0) {
                        //interest posted right away
                        $interest = GeneralHelper::savings_account_balance($saving->id) * $key->interest_rate / 100;
                        $last_interest = SavingTransaction::where('savings_id', $saving->id)->where('type',
                            'interest')->where('system_interest', 1)->orderBy('date', 'desc')->first();
                        //check number of months passed
                        $d = new \DateTime(date("Y-m-d"));
                        if ($key->interest_adding == 0) {
                            $interest_adding = $d->format('t');
                        } else {
                            $interest_adding = $key->interest_adding;
                        }
                        if (empty($last_interest)) {

                            if (str_replace("0", "", date("d")) == $interest_adding) {
                                $savings_transaction = new SavingTransaction();
                                $savings_transaction->borrower_id = $saving->borrower_id;
                                $savings_transaction->branch_id = $saving->branch_id;
                                $savings_transaction->savings_id = $saving->id;
                                $savings_transaction->system_interest = 1;
                                $savings_transaction->type = "interest";
                                $savings_transaction->reversible = 1;
                                $savings_transaction->date = date("Y-m-d");
                                $savings_transaction->time = date("H:i");
                                $date = explode('-', date("Y-m-d"));
                                $savings_transaction->year = $date[0];
                                $savings_transaction->month = $date[1];
                                $savings_transaction->credit = $interest;
                                $savings_transaction->notes = $key->interest_rate . " Per Annum Interest calculated";
                                $savings_transaction->save();
                                if (!empty($key->chart_reference)) {
                                    $journal = new JournalEntry();
                                    $journal->account_id = $key->chart_reference->id;
                                    $journal->branch_id = $savings_transaction->branch_id;
                                    $journal->date = date("Y-m-d");
                                    $journal->year = $date[0];
                                    $journal->month = $date[1];
                                    $journal->borrower_id = $savings_transaction->borrower_id;
                                    $journal->transaction_type = 'interest';
                                    $journal->name = "Savings Interest";
                                    $journal->savings_id = $saving->id;
                                    $journal->credit = $interest;
                                    $journal->reference = $savings_transaction->id;
                                    $journal->save();
                                }
                                if (!empty($key->chart_control)) {
                                    $journal = new JournalEntry();
                                    $journal->account_id = $key->chart_control->id;
                                    $journal->branch_id = $savings_transaction->branch_id;
                                    $journal->date = date("Y-m-d");
                                    $journal->year = $date[0];
                                    $journal->month = $date[1];
                                    $journal->borrower_id = $savings_transaction->borrower_id;
                                    $journal->transaction_type = 'interest';
                                    $journal->name = "Savings Interest";
                                    $journal->savings_id = $saving->id;
                                    $journal->debit = $interest;
                                    $journal->reference = $savings_transaction->id;
                                    $journal->save();
                                }
                            }

                        } else {
                            $diff = GeneralHelper::diff_in_months(new \DateTime(date("Y-m-d")),
                                new \DateTime($last_interest->date));
                            if (str_replace("0", "",
                                    date("d")) == $interest_adding && $diff >= $key->interest_posting && $diff <= $key->interest_posting + 1
                            ) {
                                $savings_transaction = new SavingTransaction();
                                $savings_transaction->borrower_id = $saving->borrower_id;
                                $savings_transaction->branch_id = $saving->branch_id;
                                $savings_transaction->savings_id = $saving->id;
                                $savings_transaction->system_interest = 1;
                                $savings_transaction->type = "interest";
                                $savings_transaction->reversible = 1;
                                $savings_transaction->date = date("Y-m-d");
                                $savings_transaction->time = date("H:i");
                                $date = explode('-', date("Y-m-d"));
                                $savings_transaction->year = $date[0];
                                $savings_transaction->month = $date[1];
                                $savings_transaction->credit = $interest;
                                $savings_transaction->notes = $key->interest_rate . " Per Annum Interest calculated";
                                $savings_transaction->save();
                                if (!empty($key->chart_reference)) {
                                    $journal = new JournalEntry();
                                    $journal->account_id = $key->chart_reference->id;
                                    $journal->branch_id = $savings_transaction->branch_id;
                                    $journal->date = date("Y-m-d");
                                    $journal->year = $date[0];
                                    $journal->month = $date[1];
                                    $journal->borrower_id = $savings_transaction->borrower_id;
                                    $journal->transaction_type = 'interest';
                                    $journal->name = "Savings Interest";
                                    $journal->savings_id = $saving->id;
                                    $journal->credit = $interest;
                                    $journal->reference = $savings_transaction->id;
                                    $journal->save();
                                }
                                if (!empty($key->chart_control)) {
                                    $journal = new JournalEntry();
                                    $journal->account_id = $key->chart_control->id;
                                    $journal->branch_id = $savings_transaction->branch_id;
                                    $journal->date = date("Y-m-d");
                                    $journal->year = $date[0];
                                    $journal->month = $date[1];
                                    $journal->borrower_id = $savings_transaction->borrower_id;
                                    $journal->transaction_type = 'interest';
                                    $journal->name = "Savings Interest";
                                    $journal->savings_id = $saving->id;
                                    $journal->debit = $interest;
                                    $journal->reference = $savings_transaction->id;
                                    $journal->save();
                                }
                            }

                        }

                    }

                }
            }
            Setting::where('setting_key',
                'cron_last_run')->update(['setting_value' => date("Y-m-d H:i:s")]);
            echo "Cron job executed successfully";
        }
    }

}
