<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;

use App\Models\Country;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Loan;
use App\Models\LoanRepayment;
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

class BorrowerController extends Controller
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
        if (!Sentinel::hasAccess('borrowers')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = Borrower::where('branch_id', session('branch_id'))->get();

        return view('borrower.data', compact('data'));
    }

    public function pending()
    {
        if (!Sentinel::hasAccess('borrowers')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = Borrower::where('branch_id', session('branch_id'))->where('active', 0)->get();

        return view('borrower.pending', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('borrowers.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $countries = array();
        foreach (Country::all() as $key) {
            $countries[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.create', compact('user', 'custom_fields','countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('borrowers.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $borrower = new Borrower();
        $borrower->first_name = $request->first_name;
        $borrower->last_name = $request->last_name;
        $borrower->user_id = Sentinel::getUser()->id;
        $borrower->gender = $request->gender;
        $borrower->country_id = $request->country_id;
        $borrower->title = $request->title;
        $borrower->branch_id = session('branch_id');
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
                $fname = "borrower_" . uniqid() .'.'. $request->file('photo')->guessExtension();
                $borrower->photo = $fname;
                $request->file('photo')->move(public_path() . '/uploads',
                    $fname);
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
        $date = explode('-', date("Y-m-d"));
        $borrower->year = $date[0];
        $borrower->month = $date[1];
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
                    $fname = "borrower_" . uniqid() .'.'. $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }
                $count++;
            }
        }
        $borrower->files = serialize($files);
        $borrower->username = $request->username;
        if (!empty($request->password)) {
            $rules = array(
                'repeatpassword' => 'required|same:password',
                'username' => 'required|unique:borrowers'
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                Flash::warning('Passwords do not match');
                return redirect()->back()->withInput()->withErrors($validator);

            } else {
                $borrower->password = md5($request->password);
            }
        }
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
        GeneralHelper::audit_trail("Added borrower  with id:" . $borrower->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/data');
    }


    public function show($borrower)
    {
        if (!Sentinel::hasAccess('borrowers.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomFieldMeta::where('category', 'borrowers')->where('parent_id', $borrower->id)->get();
        return view('borrower.show', compact('borrower', 'user', 'custom_fields'));
    }


    public function edit($borrower)
    {
        if (!Sentinel::hasAccess('borrowers.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $countries = array();
        foreach (Country::all() as $key) {
            $countries[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.edit', compact('borrower', 'user', 'custom_fields','countries'));
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
        if (!Sentinel::hasAccess('borrowers.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $borrower = Borrower::find($id);
        $borrower->first_name = $request->first_name;
        $borrower->last_name = $request->last_name;
        $borrower->gender = $request->gender;
        $borrower->country_id = $request->country_id;
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
                $fname = "borrower_" . uniqid().'.'.$request->file('photo')->guessExtension();
                $borrower->photo = $fname;
                $request->file('photo')->move(public_path() . '/uploads',
                    $fname);
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
        $files = unserialize($borrower->files);
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
                    $fname = "borrower_" . uniqid() .'.'. $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }

            }
        }
        $borrower->files = serialize($files);
        $borrower->username = $request->username;
        if (!empty($request->password)) {
            $rules = array(
                'repeatpassword' => 'required|same:password'
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                Flash::warning('Passwords do not match');
                return redirect()->back()->withInput()->withErrors($validator);

            } else {
                $borrower->password = md5($request->password);
            }
        }
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
        GeneralHelper::audit_trail("Updated borrower  with id:" . $borrower->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/data');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('borrowers.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        Borrower::destroy($id);
        Loan::where('borrower_id', $id)->delete();
        LoanRepayment::where('borrower_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted borrower  with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('borrower/data');
    }

    public function deleteFile(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $borrower = Borrower::find($id);
        $files = unserialize($borrower->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $borrower->files = serialize($files);
        $borrower->save();


    }

    public function approve(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.approve')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $borrower = Borrower::find($id);
        $borrower->active = 1;
        $borrower->save();
        GeneralHelper::audit_trail("Approved borrower  with id:" . $borrower->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function decline(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.approve')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $borrower = Borrower::find($id);
        $borrower->active = 0;
        $borrower->save();
        GeneralHelper::audit_trail("Declined borrower  with id:" . $borrower->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }
    public function blacklist(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.blacklist')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $borrower = Borrower::find($id);
        $borrower->blacklisted = 1;
        $borrower->save();
        GeneralHelper::audit_trail("Blacklisted borrower  with id:" . $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function unBlacklist(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.blacklist')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $borrower = Borrower::find($id);
        $borrower->blacklisted = 0;
        $borrower->save();
        GeneralHelper::audit_trail("Undo Blacklist for borrower  with id:" . $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }
}
