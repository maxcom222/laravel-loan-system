<?php

namespace App\Http\Controllers;

use App\Models\Borrower;

use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Charge;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class ChargeController extends Controller
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
        $data = Charge::all();

        return view('charge.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('charge.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $charge = new Charge();
        $charge->user_id = $request->user_id;
        $charge->name = $request->name;
        $charge->product = $request->product;
        $charge->amount = $request->amount;
        if ($request->product == "loan") {
            $charge->charge_type = $request->loan_charge_type;
            $charge->charge_option = $request->loan_charge_option;
        }
        if ($request->product == "savings") {
            $charge->charge_type = $request->savings_charge_type;
            $charge->charge_option = $request->savings_charge_option;
        }
        $charge->active = $request->active;
        $charge->penalty = $request->penalty;
        $charge->override = $request->override;
        $charge->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('charge/data');
    }


    public function show($charge)
    {

    }


    public function edit($charge)
    {

        return view('charge.edit', compact('charge'));
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
        $charge = Charge::find($id);
        $charge->name = $request->name;
        $charge->product = $request->product;
        $charge->amount = $request->amount;
        if ($request->product == "loan") {
            $charge->charge_type = $request->loan_charge_type;
            $charge->charge_option = $request->loan_charge_option;
        }
        if ($request->product == "savings") {
            $charge->charge_type = $request->savings_charge_type;
            $charge->charge_option = $request->savings_charge_option;
        }
        $charge->active = $request->active;
        $charge->penalty = $request->penalty;
        $charge->override = $request->override;
        $charge->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('charge/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        Charge::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('charge/data');
    }

}
