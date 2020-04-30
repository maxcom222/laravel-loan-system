<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;
use App\Models\Supplier;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Supplier::all();
       
        return view('supplier.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

      
        return view('supplier.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->mobile_phone = $request->mobile_phone;
        $supplier->work_phone = $request->work_phone;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->notes = $request->notes;
        $supplier->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('supplier/data');
    }


    public function show($supplier)
    {

        return view('supplier.show', compact('supplier'));
    }


    public function edit($supplier)
    {

        return view('supplier.edit', compact('supplier'));
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
        $supplier = Supplier::find($id);
        $supplier->name = $request->name;
        $supplier->mobile_phone = $request->mobile_phone;
        $supplier->work_phone = $request->work_phone;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->notes = $request->notes;
        $supplier->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('supplier/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        Supplier::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('supplier/data');
    }

}
