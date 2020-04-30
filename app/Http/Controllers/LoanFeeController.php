<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Models\Borrower;

use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\LoanFee;
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

class LoanFeeController extends Controller
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
        $data = LoanFee::all();

        return view('loan_fee.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('loan_fee.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loan_fee = new LoanFee();
        $loan_fee->name = $request->name;
        $loan_fee->loan_fee_type = $request->loan_fee_type;
        $loan_fee->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_fee/data');
    }


    public function show($loan_fee)
    {

    }


    public function edit($loan_fee)
    {

        return view('loan_fee.edit', compact('loan_fee'));
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
        $loan_fee = LoanFee::find($id);
        $loan_fee->name = $request->name;
        $loan_fee->loan_fee_type = $request->loan_fee_type;
        $loan_fee->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_fee/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        LoanFee::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/loan_fee/data');
    }

}
