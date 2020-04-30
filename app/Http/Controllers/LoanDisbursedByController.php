<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Models\LoanDisbursedBy;
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

class LoanDisbursedByController extends Controller
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
        $data = LoanDisbursedBy::all();

        return view('loan_disbursed_by.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        //get custom fields
        return view('loan_disbursed_by.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loan_disbursed_by = new LoanDisbursedBy();
        $loan_disbursed_by->name = $request->name;
        $loan_disbursed_by->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_disbursed_by/data');
    }


    public function show($loan_disbursed_by)
    {

        return view('loan_disbursed_by.show', compact('loan_disbursed_by'));
    }


    public function edit($loan_disbursed_by)
    {
        return view('loan_disbursed_by.edit', compact('loan_disbursed_by'));
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
        $loan_disbursed_by = LoanDisbursedBy::find($id);
        $loan_disbursed_by->name = $request->name;
        $loan_disbursed_by->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_disbursed_by/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        LoanDisbursedBy::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/loan_disbursed_by/data');
    }

}
