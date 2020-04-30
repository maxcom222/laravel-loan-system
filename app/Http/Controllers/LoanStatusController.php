<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\LoanStatus;
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

class LoanStatusController extends Controller
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
        $data = LoanStatus::all();

        return view('loan_status.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        //get custom fields
        return view('loan_status.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loan_status = new LoanStatus();
        $loan_status->name = $request->name;
        $loan_status->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_status/data');
    }


    public function show($loan_status)
    {

        return view('loan_status.show', compact('loan_status'));
    }


    public function edit($loan_status)
    {
        return view('loan_status.edit', compact('loan_status'));
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
        $loan_status = LoanStatus::find($id);
        $loan_status->name = $request->name;
        $loan_status->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_status/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        LoanStatus::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/loan_status/data');
    }

}
