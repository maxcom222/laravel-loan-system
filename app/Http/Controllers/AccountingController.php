<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;

use App\Models\ChartOfAccount;
use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\OtherIncome;
use App\Models\Payroll;
use App\Models\SavingTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Api\ClickatellHttp;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trial_balance(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('accounting.trial_balance',
            compact('start_date',
                'end_date'));
    }
    public function journal(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $account_id=$request->account_id;
        $chart_of_accounts = [];
        foreach (ChartOfAccount::all() as $key) {
            $chart_of_accounts[$key->id] = $key->name;
        }
        if($request->isMethod('post')){
            $data=JournalEntry::where('reversed', 0)->where('account_id', $request->account_id)->whereBetween('date',[$start_date,$end_date])->get();
        }else{
            $data=[];
        }
        return view('accounting.journal',
            compact('start_date',
                'end_date','chart_of_accounts','data','account_id'));
    }
    public function ledger(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('accounting.ledger',
            compact('start_date',
                'end_date'));
    }
    public function create_manual_entry()
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }

        $chart_of_accounts = [];
        foreach (ChartOfAccount::all() as $key) {
            $chart_of_accounts[$key->id] = $key->name;
        }
        return view('accounting.create_manual_entry',
            compact('chart_of_accounts'));
    }
    public function store_manual_entry(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }

        $journal = new JournalEntry();
        $journal->user_id = Sentinel::getUser()->id;
        $journal->account_id = $request->credit_account_id;
        $date = explode('-', $request->date);
        $journal->date = $request->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'manual_entry';
        $journal->name = $request->name;
        $journal->credit = $request->amount;
        $journal->reference = $request->reference;
        $journal->save();

        $journal = new JournalEntry();
        $journal->user_id = Sentinel::getUser()->id;
        $journal->account_id = $request->debit_account_id;
        $date = explode('-', $request->date);
        $journal->date = $request->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'manual_entry';
        $journal->name = $request->name;
        $journal->reference = $request->reference;
        $journal->debit = $request->amount;
        $journal->save();
        Flash::success(trans('general.successfully_saved'));
        GeneralHelper::audit_trail("Added Journal Manual Entry  with id:" . $journal->id);
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect('accounting/journal');
    }



}
