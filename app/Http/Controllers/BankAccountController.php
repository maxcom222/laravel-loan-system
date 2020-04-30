<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Tax;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class BankAccountController extends Controller
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
        if (!Sentinel::hasAccess('capital')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = BankAccount::all();
        return view('bank.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('capital.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return view('bank.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('capital.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $bank = new BankAccount();
        $bank->name = $request->name;
        $bank->notes = $request->notes;
        $bank->save();
        Flash::success("Successfully Saved");
        return redirect('capital/bank/data');
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


    public function edit($bank)
    {
        if (!Sentinel::hasAccess('capital.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return View::make('bank.edit', compact('bank'))->render();
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
        if (!Sentinel::hasAccess('capital.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $bank = BankAccount::find($id);
        $bank->name = $request->name;
        $bank->notes = $request->notes;
        $bank->save();
        Flash::success("Successfully Saved");
        return redirect('capital/bank/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('capital.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        BankAccount::destroy($id);
        Flash::success("Successfully Deleted");
        return redirect('capital/bank/data');
    }
}
