<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\AuditTrail;
use App\Models\Client;
use App\Models\ClientBeneficiary;
use App\Models\ClientIdentity;
use App\Models\ClientKin;
use App\Models\Loan;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class AuditTrailController extends Controller
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
        $data = AuditTrail::where('branch_id', session('branch_id'))->get();
        return view('audit_trail.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


}
