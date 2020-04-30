<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;

use App\Models\Capital;
use App\Models\ChartOfAccount;
use App\Models\CustomField;
use App\Models\BankAccount;
use App\Models\CustomFieldMeta;

use App\Models\JournalEntry;
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

class CapitalController extends Controller
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
    public function index()
    {
        if (!Sentinel::hasAccess('capital')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = Capital::where('branch_id', session('branch_id'))->get();
        $balance = Capital::where('branch_id', session('branch_id'))->where('type', 'deposit')->sum('amount') - Capital::where('branch_id', session('branch_id'))->where('type',
                'withdrawal')->sum('amount');
        return view('capital.data', compact('data','balance'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('capital.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $chart = [];
        $chart_expenses = array();
        foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
            $chart_expenses[$key->id] = $key->name;
        }
        $chart_income = array();
        foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
            $chart_income[$key->id] = $key->name;
        }
        $chart_liability = array();
        foreach (ChartOfAccount::where('account_type', 'liability')->get() as $key) {
            $chart_liability[$key->id] = $key->name;
        }
        $chart_equity = array();
        foreach (ChartOfAccount::where('account_type', 'equity')->get() as $key) {
            $chart_equity[$key->id] = $key->name;
        }
        $chart_assets = array();
        foreach (ChartOfAccount::where('account_type', 'asset')->get() as $key) {
            $chart_assets[$key->id] = $key->name;
        }
        $chart[trans_choice('asset',2)]=$chart_assets;
        $chart[trans_choice('income',2)]=$chart_income;
        $chart[trans_choice('liability',2)]=$chart_liability;
        $chart[trans_choice('equity',2)]=$chart_equity;
        $chart[trans_choice('expense',2)]=$chart_expenses;
        return view('capital.create', compact('banks','chart'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('capital.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $capital = new Capital();
        $capital->user_id = Sentinel::getUser()->id;
        $capital->amount = $request->amount;
        $capital->branch_id =session('branch_id');
        $capital->debit_account_id = $request->debit_account_id;
        $capital->credit_account_id = $request->credit_account_id;
        $capital->notes = $request->notes;
        $capital->date = $request->date;
        $date = explode('-', $request->date);
        $capital->year = $date[0];
        $capital->month = $date[1];
        $capital->save();
        //debit and credit the necessary accounts here
        if (!empty($capital->credit_chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $capital->credit_chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'capital';
            $journal->name = "Capital";
            $journal->capital_id = $capital->id;
            $journal->credit = $request->amount;
            $journal->reference = $capital->id;
            $journal->save();
        }
        if (!empty($capital->debit_chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $capital->debit_chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'capital';
            $journal->name = "Capital";
            $journal->capital_id = $capital->id;
            $journal->debit = $request->amount;
            $journal->reference = $capital->id;
            $journal->save();
        }
        Flash::success(trans('general.successfully_saved'));
        GeneralHelper::audit_trail("Added Capital  with id:" . $capital->id);
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect('capital/data');
    }


    public function show($capital)
    {
        if (!Sentinel::hasAccess('capital.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return view('capital.show', compact(''));
    }


    public function edit($capital)
    {
        if (!Sentinel::hasAccess('capital.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $chart = [];
        $chart_expenses = array();
        foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
            $chart_expenses[$key->id] = $key->name;
        }
        $chart_income = array();
        foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
            $chart_income[$key->id] = $key->name;
        }
        $chart_liability = array();
        foreach (ChartOfAccount::where('account_type', 'liability')->get() as $key) {
            $chart_liability[$key->id] = $key->name;
        }
        $chart_equity = array();
        foreach (ChartOfAccount::where('account_type', 'equity')->get() as $key) {
            $chart_equity[$key->id] = $key->name;
        }
        $chart_assets = array();
        foreach (ChartOfAccount::where('account_type', 'asset')->get() as $key) {
            $chart_assets[$key->id] = $key->name;
        }
        $chart[trans_choice('asset',2)]=$chart_assets;
        $chart[trans_choice('income',2)]=$chart_income;
        $chart[trans_choice('liability',2)]=$chart_liability;
        $chart[trans_choice('equity',2)]=$chart_equity;
        $chart[trans_choice('expense',2)]=$chart_expenses;
        return view('capital.edit', compact('capital','banks','chart'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('capital.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $capital = Capital::find($id);
        $capital->amount = $request->amount;
        $capital->debit_account_id = $request->debit_account_id;
        $capital->credit_account_id = $request->credit_account_id;
        $capital->notes = $request->notes;
        $capital->date = $request->date;
        $date = explode('-', $request->date);
        $capital->year = $date[0];
        $capital->month = $date[1];
        $capital->save();
        JournalEntry::where('capital_id', $id)->delete();
        if (!empty($capital->credit_chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $capital->credit_chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'capital';
            $journal->name = "Capital";
            $journal->capital_id = $capital->id;
            $journal->credit = $request->amount;
            $journal->reference = $capital->id;
            $journal->save();
        }
        if (!empty($capital->debit_chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $capital->debit_chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'capital';
            $journal->name = "Capital";
            $journal->capital_id = $capital->id;
            $journal->debit = $request->amount;
            $journal->reference = $capital->id;
            $journal->save();
        }
        GeneralHelper::audit_trail("Updated Capital  with id:" . $capital->id);
        Flash::success(trans('general.successfully_saved'));
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect('capital/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('capital.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        Capital::destroy($id);
        JournalEntry::where('capital_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted Capital  with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('capital/data');
    }


}
