<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;

use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
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

class CollateralController extends Controller
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
        if (!Sentinel::hasAccess('collateral')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = Collateral::all();

        return view('collateral.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        if (!Sentinel::hasAccess('collateral.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $types = array();
        foreach (CollateralType::all() as $key) {
            $types[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'collateral')->get();
        return view('collateral.create', compact('types', 'custom_fields', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('collateral.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $collateral = new Collateral();
        $collateral->collateral_type_id = $request->collateral_type_id;
        $collateral->name = $request->name;
        $collateral->loan_id = $loan->id;
        $collateral->borrower_id = $loan->borrower_id;
        $collateral->value = $request->value;
        $collateral->status = $request->status;
        $collateral->serial_number = $request->serial_number;
        $collateral->model_name = $request->model_name;
        $collateral->model_number = $request->model_number;
        $collateral->manufacture_date = $request->manufacture_date;
        $collateral->date = $request->date;
        $date = explode('-', $request->date);
        $collateral->year = $date[0];
        $collateral->month = $date[1];
        if ($request->hasFile('photo')) {
            $file = array('photo' => Input::file('photo'));
            $rules = array('photo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $fname = "collateral_" . uniqid() . '.' . $request->file('photo')->guessExtension();
                $collateral->photo = $fname;
                $request->file('photo')->move(public_path() . '/uploads',
                    $fname);
            }

        }
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
                    $fname = "collateral_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }
                $count++;
            }
        }
        $collateral->files = serialize($files);
        $collateral->save();
        $custom_fields = CustomField::where('category', 'collateral')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $collateral->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "collateral";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Added collateral  with id:" . $collateral->id);
        Flash::success(trans('general.successfully_saved'));
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect('collateral/data');
    }


    public function show($collateral)
    {
        if (!Sentinel::hasAccess('collateral.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        //get custom fields
        $custom_fields = CustomFieldMeta::where('category', 'collateral')->where('parent_id', $collateral->id)->get();
        return view('collateral.show', compact('collateral', 'custom_fields'));
    }


    public function edit($collateral)
    {
        if (!Sentinel::hasAccess('collateral.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $types = array();
        foreach (CollateralType::all() as $key) {
            $types[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'collateral')->get();
        return view('collateral.edit', compact('collateral', 'types', 'custom_fields'));
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
        if (!Sentinel::hasAccess('collateral.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $collateral = Collateral::find($id);
        $collateral->collateral_type_id = $request->collateral_type_id;
        $collateral->name = $request->name;
        $collateral->value = $request->value;
        $collateral->status = $request->status;
        $collateral->serial_number = $request->serial_number;
        $collateral->model_name = $request->model_name;
        $collateral->model_number = $request->model_number;
        $collateral->manufacture_date = $request->manufacture_date;
        $collateral->date = $request->date;
        $date = explode('-', $request->date);
        $collateral->year = $date[0];
        $collateral->month = $date[1];
        if ($request->hasFile('photo')) {
            $file = array('photo' => Input::file('photo'));
            $rules = array('photo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $fname = "collateral_" . uniqid() . '.' . $request->file('photo')->guessExtension();
                $collateral->photo = $fname;
                $request->file('photo')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $files = unserialize($collateral->files);
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
                    $fname = "collateral_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }

            }
        }
        $collateral->files = serialize($files);
        $collateral->save();
        $custom_fields = CustomField::where('category', 'collateral')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'collateral')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'collateral')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "collateral";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Updated collateral  with id:" . $collateral->id);
        Flash::success(trans('general.successfully_saved'));
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect('collateral/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('collateral.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        Collateral::destroy($id);
        GeneralHelper::audit_trail("Deleted collateral  with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('collateral/data');
    }

    public function deleteFile(Request $request, $id)
    {
        if (!Sentinel::hasAccess('collateral.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $collateral = Collateral::find($id);
        $files = unserialize($collateral->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $collateral->files = serialize($files);
        $collateral->save();


    }

    //expense type
    public function indexType()
    {
        $data = CollateralType::all();

        return view('collateral.type.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createType()
    {

        return view('collateral.type.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeType(Request $request)
    {
        $type = new CollateralType();
        $type->name = $request->name;
        $type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('collateral/type/data');
    }

    public function editType($collateral_type)
    {
        return view('collateral.type.edit', compact('collateral_type'));
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
        $type = CollateralType::find($id);
        $type->name = $request->name;
        $type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('collateral/type/data');
    }

    public function deleteType($id)
    {
        CollateralType::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('collateral/type/data');
    }
}
