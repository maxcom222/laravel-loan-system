<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\LoanRepaymentMethod;
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

class LoanRepaymentMethodController extends Controller
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
        $data = LoanRepaymentMethod::all();

        return view('loan_repayment_method.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        //get custom fields
        return view('loan_repayment_method.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loan_repayment_method = new LoanRepaymentMethod();
        $loan_repayment_method->name = $request->name;
        $loan_repayment_method->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_repayment_method/data');
    }


    public function show($loan_repayment_method)
    {

        return view('loan_repayment_method.show', compact('loan_repayment_method'));
    }


    public function edit($loan_repayment_method)
    {
        return view('loan_repayment_method.edit', compact('loan_repayment_method'));
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
        $loan_repayment_method = LoanRepaymentMethod::find($id);
        $loan_repayment_method->name = $request->name;
        $loan_repayment_method->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_repayment_method/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        LoanRepaymentMethod::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/loan_repayment_method/data');
    }

}
