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
use App\Models\OtherIncome;
use App\Models\OtherIncomeType;
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

class OtherIncomeController extends Controller
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
        if (!Sentinel::hasAccess('other_income')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = OtherIncome::all();

        return view('other_income.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('other_income.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $types = array();
        foreach (OtherIncomeType::all() as $key) {
            $types[$key->id] = $key->name;
        }
        $chart_assets = array();
        foreach (ChartOfAccount::where('account_type', 'asset')->get() as $key) {
            $chart_assets[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'other_income')->get();
        return view('other_income.create', compact('types', 'custom_fields', 'chart_assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('other_income.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $other_income = new OtherIncome();
        $other_income->other_income_type_id = $request->other_income_type_id;
        $other_income->account_id = $request->account_id;
        $other_income->amount = $request->amount;
        $other_income->notes = $request->notes;
        $other_income->date = $request->date;
        $date = explode('-', $request->date);
        $other_income->year = $date[0];
        $other_income->month = $date[1];
        $files = array();
        if (!empty(array_filter($request->file('files')))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = "income_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }
                $count++;
            }
        }
        $other_income->files = serialize($files);
        $other_income->save();
        $custom_fields = CustomField::where('category', 'other_income')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $other_income->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "other_income";
            $custom_field->save();
        }
        $other_income=OtherIncome::find($other_income->id);
        //debit and credit the necessary accounts
        if (!empty($other_income->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $other_income->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'income';
            $journal->name = "Other Income";
            $journal->other_income_id = $other_income->id;
            $journal->debit = $request->amount;
            $journal->reference = $other_income->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        if (!empty($other_income->other_income_type->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $other_income->other_income_type->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'income';
            $journal->name = "Other Income";
            $journal->other_income_id = $other_income->id;
            $journal->credit = $request->amount;
            $journal->reference = $other_income->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        GeneralHelper::audit_trail("Added other income with id:" . $other_income->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('other_income/data');
    }


    public function show($other_income)
    {
        if (!Sentinel::hasAccess('other_income.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'other_income')->get();
        return view('other_income.show', compact('other_income', 'user', 'custom_fields'));
    }


    public function edit($other_income)
    {
        if (!Sentinel::hasAccess('other_income.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $types = array();
        foreach (OtherIncomeType::all() as $key) {
            $types[$key->id] = $key->name;
        }
        $chart_assets = array();
        foreach (ChartOfAccount::where('account_type', 'asset')->get() as $key) {
            $chart_assets[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'other_income')->get();
        return view('other_income.edit', compact('other_income', 'types', 'custom_fields', 'chart_assets'));
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
        if (!Sentinel::hasAccess('other_income.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $other_income = OtherIncome::find($id);
        $other_income->other_income_type_id = $request->other_income_type_id;
        $other_income->account_id = $request->account_id;
        $other_income->amount = $request->amount;
        $other_income->notes = $request->notes;
        $other_income->date = $request->date;
        $date = explode('-', $request->date);
        $other_income->year = $date[0];
        $other_income->month = $date[1];
        $files = unserialize($other_income->files);
        $count = count($files);
        if (!empty(array_filter($request->file('files')))) {
            foreach ($request->file('files') as $key) {
                $count++;
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = "income_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }

            }
        }
        $other_income->files = serialize($files);
        $other_income->save();
        $other_income=OtherIncome::find($other_income->id);
        JournalEntry::where('other_income_id', $id)->delete();
        //debit and credit the necessary accounts
        if (!empty($other_income->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $other_income->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'income';
            $journal->name = "Other Income";
            $journal->other_income_id = $other_income->id;
            $journal->debit = $request->amount;
            $journal->reference = $other_income->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        if (!empty($other_income->other_income_type->chart)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $other_income->other_income_type->chart->id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'income';
            $journal->name = "Other Income";
            $journal->other_income_id = $other_income->id;
            $journal->credit = $request->amount;
            $journal->reference = $other_income->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        $custom_fields = CustomField::where('category', 'other_income')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'other_income')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'other_income')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "other_income";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Updated other income with id:" . $other_income->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('other_income/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('other_income.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        OtherIncome::destroy($id);
        JournalEntry::where('other_income_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted other income with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('other_income/data');
    }

    public function deleteFile(Request $request, $id)
    {
        if (!Sentinel::hasAccess('other_income.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $other_income = OtherIncome::find($id);
        $files = unserialize($other_income->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $other_income->files = serialize($files);
        $other_income->save();


    }

    //expense type
    public function indexType()
    {
        $data = OtherIncomeType::all();

        return view('other_income.type.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createType()
    {

        $chart_income = array();
        foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
            $chart_income[$key->id] = $key->name;
        }
        return view('other_income.type.create', compact('chart_income'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeType(Request $request)
    {
        $type = new OtherIncomeType();
        $type->name = $request->name;
        $type->account_id = $request->account_id;
        $type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('other_income/type/data');
    }

    public function editType($other_income_type)
    {

        $chart_income = array();
        foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
            $chart_income[$key->id] = $key->name;
        }
        return view('other_income.type.edit', compact('other_income_type', 'chart_income'));
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
        $type = OtherIncomeType::find($id);
        $type->name = $request->name;
        $type->account_id = $request->account_id;
        $type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('other_income/type/data');
    }

    public function deleteType($id)
    {
        OtherIncomeType::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('other_income/type/data');
    }
}
