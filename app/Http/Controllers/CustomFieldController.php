<?php

namespace App\Http\Controllers;


use App\Models\CustomField;
use App\Models\Setting;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class CustomFieldController extends Controller
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
        if (!Sentinel::hasAccess('custom_fields')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = CustomField::all();

        return view('custom_field.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('custom_fields.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return view('custom_field.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('custom_fields.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $custom_field = new CustomField();
        $custom_field->name = $request->name;
        $custom_field->category = $request->category;
        $custom_field->user_id = $request->user_id;
        $custom_field->field_type = $request->field_type;
        $custom_field->required = $request->required;
        $custom_field->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('custom_field/data');
    }


    public function show($custom_field)
    {
        return view('custom_field.show', compact('custom_field'));
    }


    public function edit($custom_field)
    {
        if (!Sentinel::hasAccess('custom_fields.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return view('custom_field.edit', compact('custom_field'));
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
        if (!Sentinel::hasAccess('custom_fields.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $custom_field = CustomField::find($id);
        $custom_field->name = $request->name;
        $custom_field->category = $request->category;
        $custom_field->field_type = $request->field_type;
        $custom_field->required = $request->required;
        $custom_field->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('custom_field/data');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('custom_fields.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        CustomField::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('custom_field/data');
    }

}
