<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;

use App\Models\ChartOfAccount;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
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

class ExpenseController extends Controller
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
        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = Expense::where('branch_id', session('branch_id'))->get();

        return view('expense.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('expenses.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $types = array();
        foreach (ExpenseType::all() as $key) {
            $types[$key->id] = $key->name;
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
        //get custom fields
        $custom_fields = CustomField::where('category', 'expenses')->get();
        return view('expense.create', compact('types', 'custom_fields','chart_assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('expenses.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $expense = new Expense();
        $expense->account_id = $request->account_id;
        $expense->expense_type_id = $request->expense_type_id;
        $expense->amount = $request->amount;
        $expense->notes = $request->notes;
        $expense->branch_id = session('branch_id');
        $expense->date = $request->date;
        $date = explode('-', $request->date);
        $expense->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $expense->recur_frequency = $request->recur_frequency;
            $expense->recur_start_date = $request->recur_start_date;
            if (!empty($request->recur_end_date)) {
                $expense->recur_end_date = $request->recur_end_date;
            }

            $expense->recur_next_date = date_format(date_add(date_create($request->recur_start_date),
                date_interval_create_from_date_string($request->recur_frequency . ' ' . $request->recur_type . 's')),
                'Y-m-d');

            $expense->recur_type = $request->recur_type;
        }
        $expense->year = $date[0];
        $expense->month = $date[1];
        $files = array();
        if (!empty($request->file('files'))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = "expense_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }
                $count++;
            }
        }
        $expense->files = serialize($files);
        //files
        $expense->save();
        $expense=Expense::find($expense->id);
        $custom_fields = CustomField::where('category', 'expenses')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $expense->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "expenses";
            $custom_field->save();
        }
        //debit and credit the necessary accounts
        if (!empty($expense->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $expense->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'expense';
            $journal->name = "Expense";
            $journal->expense_id = $expense->id;
            $journal->credit = $request->amount;
            $journal->reference = $expense->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        if (!empty($expense->expense_type->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $expense->expense_type->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'expense';
            $journal->name = "Expense";
            $journal->expense_id = $expense->id;
            $journal->debit = $request->amount;
            $journal->reference = $expense->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        GeneralHelper::audit_trail("Added expense with id:" . $expense->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/data');
    }


    public function show($borrower)
    {
        if (!Sentinel::hasAccess('expenses.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.show', compact('borrower', 'user', 'custom_fields'));
    }


    public function edit($expense)
    {
        $types = array();
        foreach (ExpenseType::all() as $key) {
            $types[$key->id] = $key->name;
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
        //get custom fields
        $custom_fields = CustomField::where('category', 'expenses')->get();
        return view('expense.edit', compact('expense', 'types', 'custom_fields','chart_assets'));
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
        if (!Sentinel::hasAccess('expenses.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $expense = Expense::find($id);
        $expense->expense_type_id = $request->expense_type_id;
        $expense->account_id = $request->account_id;
        $expense->amount = $request->amount;
        $expense->notes = $request->notes;
        $expense->date = $request->date;
        $date = explode('-', $request->date);
        $expense->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $expense->recur_frequency = $request->recur_frequency;
            $expense->recur_start_date = $request->recur_start_date;
            if (!empty($request->recur_end_date)) {
                $expense->recur_end_date = $request->recur_end_date;
            }
            if (empty($expense->recur_next_date)) {
                $expense->recur_next_date = date_format(date_add(date_create($request->recur_start_date),
                    date_interval_create_from_date_string($request->recur_frequency . ' ' . $request->recur_type . 's')),
                    'Y-m-d');
            }
            $expense->recur_type = $request->recur_type;
        }
        $expense->year = $date[0];
        $expense->month = $date[1];
        $files = unserialize($expense->files);
        $count = count($files);
        if (!empty($request->file('files'))) {
            foreach ($request->file('files') as $key) {
                $count++;
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = "expense_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }

            }
        }
        $expense->files = serialize($files);
        $expense->save();
        JournalEntry::where('expense_id', $id)->delete();
        //debit and credit the necessary accounts
        if (!empty($expense->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $expense->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'expense';
            $journal->name = "Expense";
            $journal->expense_id = $expense->id;
            $journal->credit = $request->amount;
            $journal->reference = $expense->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        if (!empty($expense->expense_type->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $expense->expense_type->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'expense';
            $journal->name = "Expense";
            $journal->expense_id = $expense->id;
            $journal->debit = $request->amount;
            $journal->reference = $expense->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        $custom_fields = CustomField::where('category', 'expenses')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'expenses')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'expenses')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "expenses";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Updated expense with id:" . $expense->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('expenses.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        Expense::destroy($id);
        JournalEntry::where('expense_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted expense with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('expense/data');
    }

    //expense type
    public function indexType()
    {
        $data = ExpenseType::all();

        return view('expense.type.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createType()
    {
        $chart_expenses = array();
        foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
            $chart_expenses[$key->id] = $key->name;
        }

        return view('expense.type.create', compact('chart_expenses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeType(Request $request)
    {
        $type = new ExpenseType();
        $type->name = $request->name;
        $type->account_id = $request->account_id;
        $type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/type/data');
    }

    public function editType($expense_type)
    {
        $chart_expenses = array();
        foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
            $chart_expenses[$key->id] = $key->name;
        }

        return view('expense.type.edit', compact('expense_type', 'chart_expenses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateType(Request $request, $id)
    {
        $type = ExpenseType::find($id);
        $type->name = $request->name;
        $type->account_id = $request->account_id;
        $type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/type/data');
    }

    public function deleteType($id)
    {
        ExpenseType::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('expense/type/data');
    }

    public function deleteFile(Request $request, $id)
    {
        $expense = Expense::find($id);
        $files = unserialize($expense->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $expense->files = serialize($files);
        $expense->save();


    }
}
