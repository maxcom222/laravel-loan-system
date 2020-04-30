<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\LoanComment;
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

class LoanCommentController extends Controller
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
        $data = LoanComment::all();

        return view('loan_comment.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
      
        //get custom fields
        return view('loan_comment.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $loan_comment = new LoanComment();
        $loan_comment->notes = $request->notes;
        $loan_comment->user_id = Sentinel::getUser()->id;
        $loan_comment->loan_id = $id;
        $loan_comment->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/'.$id.'/show');
    }


    public function show($loan_comment)
    {

        return view('loan_comment.show', compact('loan_comment'));
    }


    public function edit($id,$loan_comment)
    {
        return view('loan_comment.edit', compact('loan_comment','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id, $cid)
    {
        $loan_comment = LoanComment::find($cid);
        $loan_comment->notes = $request->notes;
        $loan_comment->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/'.$id.'/show');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id,$cid)
    {
        LoanComment::destroy($cid);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/'.$id.'/show');
    }

}
