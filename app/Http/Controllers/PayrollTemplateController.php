<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Models\Borrower;

use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\PayrollTemplate;
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

class PayrollTemplateController extends Controller
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
        $data = PayrollTemplate::all();

        return view('payroll_template.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.create', compact('user', 'custom_fields'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $borrower = new Borrower();
        $borrower->first_name = $request->first_name;
        $borrower->last_name = $request->last_name;
        $borrower->user_id = Sentinel::getUser()->id;
        $borrower->gender = $request->gender;
        $borrower->country = $request->country;
        $borrower->title = $request->title;
        $borrower->mobile = $request->mobile;
        $borrower->notes = $request->notes;
        $borrower->email = $request->email;
        if ($request->hasFile('photo')) {
            $file = array('photo' => Input::file('photo'));
            $rules = array('photo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $borrower->photo = $request->file('photo')->getClientOriginalName();
                $request->file('photo')->move(public_path() . '/uploads',
                    $request->file('photo')->getClientOriginalName());
            }

        }
        $borrower->unique_number = $request->unique_number;
        $borrower->dob = $request->dob;
        $borrower->address = $request->address;
        $borrower->city = $request->city;
        $borrower->state = $request->state;
        $borrower->zip = $request->zip;
        $borrower->phone = $request->phone;
        $borrower->business_name = $request->business_name;
        $borrower->working_status = $request->working_status;
        $borrower->loan_officers = serialize($request->loan_officers);
        $borrower->save();
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $borrower->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "borrowers";
            $custom_field->save();
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/data');
    }


    public function show($borrower)
    {
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.show', compact('borrower', 'user', 'custom_fields'));
    }


    public function edit($borrower)
    {
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.edit', compact('borrower', 'user', 'custom_fields'));
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
        $borrower = Borrower::find($id);
        $borrower->first_name = $request->first_name;
        $borrower->last_name = $request->last_name;
        $borrower->gender = $request->gender;
        $borrower->country = $request->country;
        $borrower->title = $request->title;
        $borrower->mobile = $request->mobile;
        $borrower->notes = $request->notes;
        $borrower->email = $request->email;
        if ($request->hasFile('photo')) {
            $file = array('photo' => Input::file('photo'));
            $rules = array('photo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $borrower->photo = $request->file('photo')->getClientOriginalName();
                $request->file('photo')->move(public_path() . '/uploads',
                    $request->file('photo')->getClientOriginalName());
            }

        }
        $borrower->unique_number = $request->unique_number;
        $borrower->dob = $request->dob;
        $borrower->address = $request->address;
        $borrower->city = $request->city;
        $borrower->state = $request->state;
        $borrower->zip = $request->zip;
        $borrower->phone = $request->phone;
        $borrower->business_name = $request->business_name;
        $borrower->working_status = $request->working_status;
        $borrower->loan_officers = serialize($request->loan_officers);
        $borrower->save();
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'borrowers')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'borrowers')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "borrowers";
            $custom_field->save();
        }
        $borrower->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/data');
    }

    public function updateQty(Request $request, $id)
    {
        $borrower = borrower::find($id);
        $borrower->qty = $request->qty;
        $borrower->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        borrower::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('borrower/data');
    }

}
