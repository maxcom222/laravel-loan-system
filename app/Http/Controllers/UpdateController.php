<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\Borrower;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\Setting;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class UpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function download()
    {

        $path = storage_path() . "/updates/update.zip";
        $url = $_REQUEST['url'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $fp = fopen($path, 'w+');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $output = curl_exec($ch);
        if ($output) {
            $msg = trans_choice('general.file_downloaded_successfully', 1);
        } else {
            $error = trans_choice('general.failed_to_download_file', 1);
        }
        curl_close($ch);
        fclose($fp);

        return view('update.download', compact('msg', 'error'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function install()
    {
        if (file_exists(storage_path() . "/updates/update.zip")) {
            //begin the update
            $zip = new \ZipArchive();
            if ($zip->open(storage_path() . "/updates/update.zip") === TRUE) {
                $res = $zip->extractTo(storage_path("updates"));
                $zip->close();
                //run new migrations
                Artisan::call('view:clear');
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('migrate');
                unlink(storage_path() . "/updates/update.zip");
                Flash::warning(trans('general.update_successful'));
                return redirect('update/finish');
            } else {
                Flash::warning(trans('general.update_file_does_not_exist'));
                return redirect()->back();
            }
        } else {
            Flash::warning(trans('general.update_file_does_not_exist'));
            return redirect()->back();
        }
        //return view('tax.create', compact(''));
    }

    public function finish()
    {

        return view('update.finish', compact(''));
    }


    public function fix()
    {
        //fix schedules
        $count = 0;
        foreach (LoanSchedule::all() as $key) {
            if (empty($key->branch_id) && !empty($key->loan)) {
                $b = LoanSchedule::find($key->id);
                $b->branch_id = $key->loan->branch_id;
                $b->save();
                $count += 1;
            }
        }
        Flash::success("Successfully fixed " . $count . " records");
        return redirect('dashboard');
    }

    public function fix_schedules()
    {
        //fix schedules
        $count = 0;
        foreach (LoanSchedule::all() as $key) {
            if (empty($key->branch_id) && !empty($key->loan)) {
                $b = LoanSchedule::find($key->id);
                $b->branch_id = $key->loan->branch_id;
                $b->save();
                $count += 1;
            }
        }
        Flash::success("Successfully fixed " . $count . " records");
        return redirect('dashboard');
    }

    public function set_default_branch()
    {
        //fix schedules
        if (!empty(Branch::first())) {
            $branch = Branch::first();
            //look for 1 admin user to give the permission
            $role = Sentinel::findRoleBySlug('admin');
            if (!empty($role)) {
                if (!empty($role->users()->with('roles')->first())) {
                    $user = $role->users()->with('roles')->orderBy('created_at', 'asc')->first();
                    $permission = new BranchUser();
                    $permission->branch_id = $branch->id;
                    $permission->user_id = $user->id;
                    $permission->save();
                    //notify user
                    Mail::raw("Default Branch permission has been assigned to you",
                        function ($message) {
                            $message->from(Setting::where('setting_key',
                                'company_email')->first()->setting_value,
                                Setting::where('setting_key', 'company_name')->first()->setting_value);
                            $message->to(Setting::where('setting_key',
                                'company_email')->first()->setting_value);
                            $headers = $message->getHeaders();
                            $message->setContentType('text/html');
                            $message->setSubject("Branch permission assigned");

                        });
                    if (!empty(Setting::where('setting_key', 'company_email')->first())) {
                        Mail::raw("Default Branch permission has been assigned to: " . $user->first_name . " " . $user->last_name,
                            function ($message) {
                                $message->from(Setting::where('setting_key',
                                    'company_email')->first()->setting_value,
                                    Setting::where('setting_key', 'company_name')->first()->setting_value);
                                $message->to(Setting::where('setting_key',
                                    'company_email')->first()->setting_value);
                                $headers = $message->getHeaders();
                                $message->setContentType('text/html');
                                $message->setSubject("Branch permission assigned");
                            });
                    }
                } else {
                    //failed to assign default user, notify admin
                    if (!empty(Setting::where('setting_key', 'company_email')->first())) {
                        Mail::raw("Failed to assign branch permission to user",
                            function ($message) {
                                $message->from(Setting::where('setting_key',
                                    'company_email')->first()->setting_value,
                                    Setting::where('setting_key', 'company_name')->first()->setting_value);
                                $message->to(Setting::where('setting_key',
                                    'company_email')->first()->setting_value);
                                $headers = $message->getHeaders();
                                $message->setContentType('text/html');
                                $message->setSubject("Failed to assign branch permission");

                            });
                    }

                }

            }
        }
        Flash::success("Successfully fixed 1 records");
        return redirect('dashboard');
    }

    public function update_2_0()
    {

        Flash::success("Starting  the update now:Step 1");
        return redirect('update_2_0_1');
    }
    public function update_2_0_1()
    {
        //fix schedules
        $count = 0;
        //first empty Journal Entries table
        JournalEntry::truncate();
        LoanTransaction::truncate();
        //import loan transactions and journals
        foreach (Loan::whereIn('loans.status',
            ['disbursed', 'closed', 'written_off', 'rescheduled'])->get() as $key) {
            //disbursement transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->user_id = $key->user_id;
            $loan_transaction->branch_id = $key->branch_id;
            $loan_transaction->loan_id = $key->id;
            $loan_transaction->borrower_id = $key->borrower_id;
            $loan_transaction->transaction_type = "disbursement";
            $loan_transaction->date = $key->disbursed_date;
            $date = explode('-', $key->disbursed_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $key->principal;
            $loan_transaction->save();
            if (!empty($key->loan_product)) {
                if (!empty($key->loan_product->chart_fund_source)) {
                    $journal = new JournalEntry();
                    $journal->user_id = $key->user_id;
                    $journal->account_id = $key->loan_product->chart_fund_source->id;
                    $journal->branch_id = $key->branch_id;
                    $journal->date = $key->disbursed_date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $key->borrower_id;
                    $journal->transaction_type = 'disbursement';
                    $journal->name = "Loan Disbursement";
                    $journal->loan_id = $key->id;
                    $journal->credit = $key->principal;
                    $journal->reference = $key->id;
                    $journal->save();
                }
                if (!empty($key->loan_product->chart_loan_portfolio)) {
                    $journal = new JournalEntry();
                    $journal->user_id = $key->user_id;
                    $journal->account_id = $key->loan_product->chart_loan_portfolio->id;
                    $journal->branch_id = $key->branch_id;
                    $journal->date = $key->disbursed_date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $key->borrower_id;
                    $journal->transaction_type = 'disbursement';
                    $journal->name = "Loan Disbursement";
                    $journal->loan_id = $key->id;
                    $journal->debit = $key->principal;
                    $journal->reference = $key->id;
                    $journal->save();
                }
                //interest transaction
                $interest = GeneralHelper::loan_total_interest($key->id);
                $loan_transaction = new LoanTransaction();
                $loan_transaction->user_id = $key->user_id;
                $loan_transaction->branch_id = $key->branch_id;
                $loan_transaction->loan_id = $key->id;
                $loan_transaction->borrower_id = $key->borrower_id;
                $loan_transaction->transaction_type = "interest";
                $loan_transaction->date = $key->disbursed_date;
                $date = explode('-', $key->disbursed_date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->debit = $interest;
                $loan_transaction->save();
                //fees transaction
                $fees = GeneralHelper::loan_total_fees($key->id);


                if ($key->loan_product->accounting_rule == "accrual_upfront") {
                    //we need to save the accrued interest in journal here
                    if (!empty($key->loan_product->chart_receivable_interest)) {
                        $journal = new JournalEntry();
                        $journal->user_id = $key->user_id;
                        $journal->account_id = $key->loan_product->chart_receivable_interest->id;
                        $journal->branch_id = $key->branch_id;
                        $journal->date = $key->disbursed_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->borrower_id = $key->borrower_id;
                        $journal->transaction_type = 'accrual';
                        $journal->name = "Accrued Interest";
                        $journal->loan_id = $key->id;
                        $journal->debit = $interest;
                        $journal->reference = $key->id;
                        $journal->save();
                    }
                    if (!empty($key->loan_product->chart_income_interest)) {
                        $journal = new JournalEntry();
                        $journal->user_id = $key->user_id;
                        $journal->account_id = $key->loan_product->chart_income_interest->id;
                        $journal->branch_id = $key->branch_id;
                        $journal->date = $key->disbursed_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->borrower_id = $key->borrower_id;
                        $journal->transaction_type = 'accrual';
                        $journal->name = "Accrued Interest";
                        $journal->loan_id = $key->id;
                        $journal->credit = $interest;
                        $journal->reference = $key->id;
                        $journal->save();
                    }

                }

            }

        }
        Flash::success("Update in progress:Step 2");
        return redirect('update_2_0_2');
    }
    public function update_2_0_2()
    {
        //fix schedules
        $count = 0;
        //first empty Journal Entries table
        //import loan transactions and journals
        foreach (Loan::whereIn('loans.status',
            ['disbursed', 'closed', 'written_off', 'rescheduled'])->get() as $key) {
            //disbursement transaction

            $date = explode('-', $key->disbursed_date);
            if (!empty($key->loan_product)) {
                //check for schedules
                foreach (LoanSchedule::where('loan_id', $key->id)->get() as $schedule) {
                    if ($schedule->fees>0) {
                        $loan_transaction = new LoanTransaction();
                        $loan_transaction->user_id = $key->user_id;
                        $loan_transaction->branch_id = $key->branch_id;
                        $loan_transaction->loan_id = $key->id;
                        $loan_transaction->borrower_id = $key->borrower_id;
                        $loan_transaction->transaction_type = "specified_due_date_fee";
                        $loan_transaction->date = $schedule->due_date;
                        $date = explode('-', $schedule->due_date);
                        $loan_transaction->year = $date[0];
                        $loan_transaction->month = $date[1];
                        $loan_transaction->debit = $schedule->fees;
                        $loan_transaction->reversible = 1;
                        $loan_transaction->save();
                    }
                    if ($schedule->penalty>0) {
                        $loan_transaction = new LoanTransaction();
                        $loan_transaction->user_id = $key->user_id;
                        $loan_transaction->branch_id =$key->branch_id;
                        $loan_transaction->loan_id = $key->id;
                        $loan_transaction->borrower_id = $key->borrower_id;
                        $loan_transaction->transaction_type = "penalty";
                        $loan_transaction->date = $schedule->due_date;
                        $date = explode('-', $schedule->due_date);
                        $loan_transaction->year = $date[0];
                        $loan_transaction->month = $date[1];
                        $loan_transaction->debit = $schedule->fees;
                        $loan_transaction->reversible = 1;
                        $loan_transaction->save();
                    }
                    //check for penalty
                }
            }

        }
        Flash::success("Update in progress:Step 3");
        return redirect('update_2_0_3');
    }
    public function update_2_0_3()
    {
        //fix schedules

        //import loan transactions and journals
        foreach (Loan::whereIn('loans.status',
            ['disbursed', 'closed', 'written_off', 'rescheduled'])->get() as $key) {
            //disbursement transaction

            if (!empty($key->loan_product)) {
                //payments
                foreach (LoanRepayment::where('loan_id', $key->id)->orderBy('collection_date','asc')->get() as $repayment) {
                    if ($repayment->amount>0) {
                        $loan_transaction = new LoanTransaction();
                        $loan_transaction->user_id = $key->user_id;
                        $loan_transaction->branch_id = $key->branch_id;
                        $loan_transaction->loan_id = $key->id;
                        $loan_transaction->borrower_id = $key->borrower_id;
                        $loan_transaction->transaction_type = "repayment";
                        $loan_transaction->date = $repayment->collection_date;
                        $date = explode('-', $repayment->collection_date);
                        $loan_transaction->year = $date[0];
                        $loan_transaction->month = $date[1];
                        $loan_transaction->repayment_method_id = $repayment->repayment_method_id;
                        $loan_transaction->receipt = $repayment->receipt;
                        $loan_transaction->notes = $repayment->notes;
                        $loan_transaction->credit = $repayment->amount;
                        $loan_transaction->reversible = 1;
                        $loan_transaction->save();
                        //journal entries
                        $allocation = GeneralHelper::loan_allocate_payment($loan_transaction);
                        //principal
                        if ($allocation['principal'] > 0) {
                            if (!empty($key->loan_product->chart_loan_portfolio)) {
                                $journal = new JournalEntry();
                                $journal->user_id = Sentinel::getUser()->id;
                                $journal->account_id = $key->loan_product->chart_loan_portfolio->id;
                                $journal->branch_id = $key->branch_id;
                                $journal->date = $repayment->collection_date;
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $key->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->transaction_sub_type = 'repayment_principal';
                                $journal->name = "Principal Repayment";
                                $journal->loan_id = $key->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->credit = $allocation['principal'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                            if (!empty($key->loan_product->chart_fund_source)) {
                                $journal = new JournalEntry();
                                $journal->user_id = Sentinel::getUser()->id;
                                $journal->account_id = $key->loan_product->chart_fund_source->id;
                                $journal->branch_id = $key->branch_id;
                                $journal->date = $repayment->collection_date;
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $key->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->name = "Principal Repayment";
                                $journal->loan_id = $key->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->debit = $allocation['principal'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                        }
                        //interest
                        if ($allocation['interest'] > 0) {
                            if (!empty($key->loan_product->chart_income_interest)) {
                                $journal = new JournalEntry();
                                $journal->user_id = Sentinel::getUser()->id;
                                $journal->account_id = $key->loan_product->chart_income_interest->id;
                                $journal->branch_id = $key->branch_id;
                                $journal->date = $repayment->collection_date;
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $key->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->transaction_sub_type = 'repayment_interest';
                                $journal->name = "Interest Repayment";
                                $journal->loan_id = $key->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->credit = $allocation['interest'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                            if (!empty($key->loan_product->chart_receivable_interest)) {
                                $journal = new JournalEntry();
                                $journal->user_id = Sentinel::getUser()->id;
                                $journal->account_id = $key->loan_product->chart_receivable_interest->id;
                                $journal->branch_id = $key->branch_id;
                                $journal->date = $repayment->collection_date;
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $key->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->name = "Interest Repayment";
                                $journal->loan_id = $key->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->debit = $allocation['interest'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                        }
                        //fees
                        if ($allocation['fees'] > 0) {
                            if (!empty($key->loan_product->chart_income_fee)) {
                                $journal = new JournalEntry();
                                $journal->user_id = Sentinel::getUser()->id;
                                $journal->account_id = $key->loan_product->chart_income_fee->id;
                                $journal->branch_id = $key->branch_id;
                                $journal->date = $repayment->collection_date;
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $key->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->transaction_sub_type = 'repayment_fees';
                                $journal->name = "Fees Repayment";
                                $journal->loan_id = $key->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->credit = $allocation['fees'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                            if (!empty($key->loan_product->chart_receivable_fee)) {
                                $journal = new JournalEntry();
                                $journal->user_id = Sentinel::getUser()->id;
                                $journal->account_id = $key->loan_product->chart_receivable_fee->id;
                                $journal->branch_id = $key->branch_id;
                                $journal->date = $repayment->collection_date;
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $key->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->name = "Fees Repayment";
                                $journal->loan_id = $key->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->debit = $allocation['fees'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                        }
                        if ($allocation['penalty'] > 0) {
                            if (!empty($key->loan_product->chart_income_penalty)) {
                                $journal = new JournalEntry();
                                $journal->user_id = Sentinel::getUser()->id;
                                $journal->account_id = $key->loan_product->chart_income_penalty->id;
                                $journal->branch_id = $key->branch_id;
                                $journal->date = $repayment->collection_date;
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $key->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->transaction_sub_type = 'repayment_penalty';
                                $journal->name = "Penalty Repayment";
                                $journal->loan_id = $key->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->credit = $allocation['penalty'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                            if (!empty($key->loan_product->chart_receivable_penalty)) {
                                $journal = new JournalEntry();
                                $journal->user_id = Sentinel::getUser()->id;
                                $journal->account_id = $key->loan_product->chart_receivable_penalty->id;
                                $journal->branch_id = $key->branch_id;
                                $journal->date = $repayment->collection_date;
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $key->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->name = "Penalty Repayment";
                                $journal->loan_id = $key->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->debit = $allocation['penalty'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                        }
                    }

                }
            }

        }
        Flash::success("Successfully updated  records");
        return redirect('dashboard');
    }
}
