<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Sentinel::hasAccess('stock')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = Warehouse::all();
        return view('warehouse.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('stock.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return view('warehouse.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('stock.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $warehouse = new Warehouse();
        $warehouse->name = $request->name;
        $warehouse->notes = $request->notes;
        $warehouse->save();
        Flash::success("Successfully Saved");
        return redirect('warehouse/data');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($warehouse)
    {
        if (!Sentinel::hasAccess('stock.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return view('warehouse.edit', compact('warehouse'));
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
        if (!Sentinel::hasAccess('stock.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $warehouse = Warehouse::find($id);
        $warehouse->name = $request->name;
        $warehouse->notes = $request->notes;
        $warehouse->save();
        Flash::success("Successfully Saved");
        return redirect('warehouse/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('stock.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        Warehouse::destroy($id);
        Flash::success("Successfully Deleted");
        return redirect('warehouse/data');
    }
}
