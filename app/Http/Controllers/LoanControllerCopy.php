<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;
use App\Helpers\Infobip;
use App\Helpers\RouteSms;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Email;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanFee;
use App\Models\LoanFeeMeta;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\LoanRepaymentMethod;
use App\Models\LoanDisbursedBy;
use App\Models\Borrower;
use App\Models\LoanSchedule;
use App\Models\Setting;
use App\Models\Sms;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Api\ClickatellHttp;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\View;
use PDF;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class LoanControllerCopy extends Controller
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
    public function index(Request $request)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        if (empty($request->status)) {
            $data = Loan::all();
        } else {
            $data = Loan::where('status', $request->status)->get();
        }

        return view('loan.data', compact('data'));
    }

    public function create(Request $request)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $borrowers = array();
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        $loan_products = array();
        foreach (LoanProduct::all() as $key) {
            $loan_products[$key->id] = $key->name;
        }

        $loan_disbursed_by = array();
        foreach (LoanDisbursedBy::all() as $key) {
            $loan_disbursed_by[$key->id] = $key->name;
        }
        if (isset($request->product_id)) {
            $loan_product = LoanProduct::find($request->product_id);
        } else {
            $loan_product = LoanProduct::first();
        }
        if (isset($request->borrower_id)) {
            $borrower_id = $request->borrower_id;
        } else {
            $borrower_id = '';
        }
        if (empty($loan_product)) {
            Flash::warning("No loan product set. You must first set a loan product");
            return redirect()->back();
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'loans')->get();
        $loan_fees = LoanFee::all();
        return view('loan.create',
            compact('borrowers', 'loan_disbursed_by', 'loan_products', 'loan_product', 'borrower_id', 'custom_fields',
                'loan_fees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $loan = new Loan();
        $loan->loan_disbursed_by_id = $request->loan_disbursed_by_id;
        $loan->principal = $request->principal;
        $loan->interest_method = $request->interest_method;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_period = $request->interest_period;
        $loan->loan_duration = $request->loan_duration;
        $loan->loan_duration_type = $request->loan_duration_type;
        $loan->repayment_cycle = $request->repayment_cycle;
        $loan->decimal_places = $request->decimal_places;
        $loan->repayment_order = $request->repayment_order;
        $loan->override_interest = $request->override_interest;
        $loan->override_interest_amount = $request->override_interest_amount;
        $loan->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan->borrower_id = $request->borrower_id;
        $loan->user_id = Sentinel::getUser()->id;
        $loan->loan_product_id = $request->loan_product_id;
        $loan->release_date = $request->release_date;
        $date = explode('-', $request->release_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        $loan->first_payment_date = $request->first_payment_date;
        $loan->description = $request->description;
        $files = array();
        if (!empty(array_filter($request->file('files')))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $files[$count] = $key->getClientOriginalName();
                    $key->move(public_path() . '/uploads',
                        $key->getClientOriginalName());
                }
                $count++;
            }
        }
        $loan->files = serialize($files);
        $loan->save();

        //save custom meta
        $custom_fields = CustomField::where('category', 'loans')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $loan->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "loans";
            $custom_field->save();
        }
        //save loan fees
        $fees_distribute = 0;
        $fees_first_payment = 0;
        $fees_last_payment = 0;
        foreach (LoanFee::all() as $key) {
            $loan_fee = new LoanFeeMeta();
            $value = 'loan_fees_amount_' . $key->id;
            $loan_fees_schedule = 'loan_fees_schedule_' . $key->id;
            $loan_fee->user_id = Sentinel::getUser()->id;
            $loan_fee->category = 'loan';
            $loan_fee->parent_id = $loan->id;
            $loan_fee->loan_fees_id = $key->id;
            $loan_fee->value = $request->$value;
            $loan_fee->loan_fees_schedule = $request->$loan_fees_schedule;
            $loan_fee->save();
            //determine amount to use
            if ($key->loan_fee_type == 'fixed') {
                if ($loan_fee->loan_fees_schedule == 'distribute_fees_evenly') {
                    $fees_distribute = $fees_distribute + $loan_fee->value;
                }
                if ($loan_fee->loan_fees_schedule == 'charge_fees_on_first_payment') {
                    $fees_first_payment = $fees_first_payment + $loan_fee->value;
                }
                if ($loan_fee->loan_fees_schedule == 'charge_fees_on_last_payment') {
                    $fees_last_payment = $fees_last_payment + $loan_fee->value;
                }
            } else {
                if ($loan_fee->loan_fees_schedule == 'distribute_fees_evenly') {
                    $fees_distribute = $fees_distribute + ($loan_fee->value * $loan->principal / 100);
                }
                if ($loan_fee->loan_fees_schedule == 'charge_fees_on_first_payment') {
                    $fees_first_payment = $fees_first_payment + ($loan_fee->value * $loan->principal / 100);
                }
                if ($loan_fee->loan_fees_schedule == 'charge_fees_on_last_payment') {
                    $fees_last_payment = $fees_last_payment + ($loan_fee->value * $loan->principal / 100);
                }
            }
        }
        //lets create schedules here
        //determine interest rate to use

        $interest_rate = GeneralHelper::determine_interest_rate($loan->id);

        $period = GeneralHelper::loan_period($loan->id);
        $loan = Loan::find($loan->id);
        if ($loan->repayment_cycle == 'daily') {
            $repayment_cycle = 'day';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' days')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'weekly') {
            $repayment_cycle = 'week';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' weeks')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'monthly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'bi_monthly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'quarterly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'semi_annually') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'yearly') {
            $repayment_cycle = 'year';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' years')),
                'Y-m-d');
        }
        $loan->save();
        //generate schedules until period finished
        $next_payment = $request->first_payment_date;
        $balance = $request->principal;
        for ($i = 1; $i <= $period; $i++) {
            $fees = 0;
            if ($i == 1) {
                $fees = $fees + ($fees_first_payment);
            }
            if ($i == $period) {
                $fees = $fees + ($fees_last_payment);
            }
            $fees = $fees + ($fees_distribute / $period);
            $loan_schedule = new LoanSchedule();
            $loan_schedule->loan_id = $loan->id;
            $loan_schedule->fees = $fees;
            $loan_schedule->borrower_id = $loan->borrower_id;
            $loan_schedule->description = 'Repayment';
            $loan_schedule->due_date = $next_payment;
            $date = explode('-', $next_payment);
            $loan_schedule->month = $date[1];
            $loan_schedule->year = $date[0];
            //determine which method to use
            $due = 0;
            //reducing balance equal installments
            if ($request->interest_method == 'declining_balance_equal_installments') {
                $due = GeneralHelper::amortized_monthly_payment($loan->id, $loan->principal);
                if ($loan->decimal_places == 'round_off_to_two_decimal') {
                    //determine if we have grace period for interest

                    $interest = round(($interest_rate * $balance), 2);
                    $loan_schedule->principal = round(($due - $interest), 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest, 2);
                    }
                    $loan_schedule->due = round($due, 2);
                    //determine next balance
                    $balance = round(($balance - ($due - $interest)), 2);
                    $loan_schedule->principal_balance = round($balance, 2);
                } else {
                    //determine if we have grace period for interest

                    $interest = round(($interest_rate * $balance));
                    $loan_schedule->principal = round(($due - $interest));
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest);
                    }
                    $loan_schedule->due = round($due);
                    //determine next balance
                    $balance = round(($balance - ($due - $interest)));
                    $loan_schedule->principal_balance = round($balance);
                }


            }
            //reducing balance equal principle
            if ($request->interest_method == 'declining_balance_equal_principal') {
                $principal = $loan->principal / $period;
                if ($loan->decimal_places == 'round_off_to_two_decimal') {

                    $interest = round(($interest_rate * $balance), 2);
                    $loan_schedule->principal = round($principal, 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest, 2);
                    }
                    $loan_schedule->due = round(($principal + $interest), 2);
                    //determine next balance
                    $balance = round(($balance - ($principal + $interest)), 2);
                    $loan_schedule->principal_balance = round($balance, 2);
                } else {

                    $loan_schedule->principal = round(($principal));

                    $interest = round(($interest_rate * $balance));
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest);
                    }
                    $loan_schedule->due = round($principal + $interest);
                    //determine next balance
                    $balance = round(($balance - ($principal + $interest)));
                    $loan_schedule->principal_balance = round($balance);
                }

            }
            //flat  method
            if ($request->interest_method == 'flat_rate') {
                $principal = $loan->principal / $period;
                if ($loan->decimal_places == 'round_off_to_two_decimal') {
                    $interest = round(($interest_rate * $loan->principal), 2);
                    $loan_schedule->principal = round(($principal), 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest, 2);
                    }
                    $loan_schedule->principal = round(($principal), 2);
                    $loan_schedule->due = round(($principal + $interest), 2);
                    //determine next balance
                    $balance = round(($balance - $principal), 2);
                    $loan_schedule->principal_balance = round($balance, 2);
                } else {
                    $interest = round(($interest_rate * $loan->principal));
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest);
                    }
                    $loan_schedule->principal = round($principal);
                    $loan_schedule->due = round($principal + $interest);
                    //determine next balance
                    $balance = round(($balance - $principal));
                    $loan_schedule->principal_balance = round($balance);
                }
            }
            //interest only method
            if ($request->interest_method == 'interest_only') {
                if ($i == $period) {
                    $principal = $loan->principal;
                } else {
                    $principal = 0;
                }
                if ($loan->decimal_places == 'round_off_to_two_decimal') {
                    $interest = round(($interest_rate * $loan->principal), 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest, 2);
                    }
                    $loan_schedule->principal = round(($principal), 2);
                    $loan_schedule->due = round(($principal + $interest), 2);
                    //determine next balance
                    $balance = round(($balance - $principal), 2);
                    $loan_schedule->principal_balance = round($balance, 2);
                } else {
                    $interest = round(($interest_rate * $loan->principal));
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest);
                    }
                    $loan_schedule->principal = round($principal);
                    $loan_schedule->due = round($principal + $interest);
                    //determine next balance
                    $balance = round(($balance - $principal));
                    $loan_schedule->principal_balance = round($balance);
                }
            }
            //determine next due date
            if ($loan->repayment_cycle == 'daily') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 days')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'weekly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 weeks')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'monthly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 months')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'bi_monthly') {
                $next_payment = date_format(date_add(date_create($request->first_payment_date),
                    date_interval_create_from_date_string($period . ' months')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'quarterly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('2 months')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'semi_annually') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('6 months')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'yearly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 years')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($i == $period) {
                $loan_schedule->principal_balance = round($balance);
            }
            $loan_schedule->save();
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/data');
    }


    public function show($loan)
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
        $custom_fields = CustomFieldMeta::where('category', 'loans')->where('parent_id', $loan->id)->get();
        return view('loan.show', compact('loan', 'schedules', 'payments', 'custom_fields'));
    }


    public function edit($loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $borrowers = array();
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        $loan_products = array();
        foreach (LoanProduct::all() as $key) {
            $loan_products[$key->id] = $key->name;
        }

        $loan_disbursed_by = array();
        foreach (LoanDisbursedBy::all() as $key) {
            $loan_disbursed_by[$key->id] = $key->name;
        }

        //get custom fields
        $custom_fields = CustomField::where('category', 'loans')->get();
        $loan_fees = LoanFee::all();
        return view('loan.edit',
            compact('loan', 'borrowers', 'loan_disbursed_by', 'loan_products', 'custom_fields', 'loan_fees'));
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
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $loan = Loan::find($id);
        $loan->loan_disbursed_by_id = $request->loan_disbursed_by_id;
        $loan->principal = $request->principal;
        $loan->interest_method = $request->interest_method;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_period = $request->interest_period;
        $loan->loan_duration = $request->loan_duration;
        $loan->loan_duration_type = $request->loan_duration_type;
        $loan->repayment_cycle = $request->repayment_cycle;
        $loan->decimal_places = $request->decimal_places;
        $loan->repayment_order = $request->repayment_order;
        $loan->override_interest = $request->override_interest;
        $loan->override_interest_amount = $request->override_interest_amount;
        $loan->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan->borrower_id = $request->borrower_id;
        $loan->loan_product_id = $request->loan_product_id;
        $loan->release_date = $request->release_date;
        $date = explode('-', $request->release_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        $loan->first_payment_date = $request->first_payment_date;
        $loan->description = $request->description;
        $files = unserialize($loan->files);
        $count = count($files);
        if (!empty(array_filter($request->file('files')))) {
            foreach ($request->file('files') as $key) {
                $count++;
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $files[$count] = $key->getClientOriginalName();
                    $key->move(public_path() . '/uploads',
                        $key->getClientOriginalName());
                }

            }
        }
        $loan->files = serialize($files);
        $loan->save();
        $custom_fields = CustomField::where('category', 'loans')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'loans')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'loans')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "loans";
            $custom_field->save();
        }
        foreach (LoanFee::all() as $key) {
            if (!empty(LoanFeeMeta::where('loan_fees_id', $key->id)->where('parent_id', $id)->where('category',
                'loan')->first())
            ) {
                $loan_fee = LoanFeeMeta::where('loan_fees_id', $key->id)->where('parent_id', $id)->where('category',
                    'loan')->first();
            } else {
                $loan_fee = new LoanFeeMeta();
            }

            $value = 'loan_fees_amount_' . $key->id;
            $loan_fees_schedule = 'loan_fees_schedule_' . $key->id;
            $loan_fee->user_id = Sentinel::getUser()->id;
            $loan_fee->category = 'loan';
            $loan_fee->parent_id = $loan->id;
            $loan_fee->loan_fees_id = $key->id;
            $loan_fee->value = $request->$value;
            $loan_fee->loan_fees_schedule = $request->$loan_fees_schedule;
            $loan_fee->save();
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        Loan::destroy($id);
        LoanSchedule::where('loan_id', $id)->delete();
        LoanRepayment::where('loan_id', $id)->delete();
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/data');
    }

    public function deleteFile(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $loan = Loan::find($id);
        $files = unserialize($loan->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $loan->files = serialize($files);
        $loan->save();


    }

    public function indexRepayment()
    {
        if (!Sentinel::hasAccess('repayments')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $data = LoanRepayment::all();

        return view('loan_repayment.data', compact('data'));
    }

    //loan repayments
    public function createBulkRepayment()
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $loans = array();
        foreach (Loan::all() as $key) {
            $loans[$key->id] = $key->borrower->first_name . ' ' . $key->borrower->last_name . '(' . trans_choice('general.loan',
                    1) . '#' . $key->id . ',' . trans_choice('general.due',
                    1) . ':' . GeneralHelper::loan_total_balance($key->id) . ')';
        }
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }
        $custom_fields = CustomField::where('category', 'repayments')->get();
        return view('loan_repayment.bulk', compact('loan', 'repayment_methods', 'custom_fields', 'loans'));
    }

    public function storeBulkRepayment(Request $request)
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        for ($i = 1; $i <= 20; $i++) {
            $amount = "repayment_amount" . $i;
            $loan_id = "loan_id" . $i;
            $repayment_method = "repayment_method_id" . $i;
            $collected_date = "repayment_collected_date" . $i;
            $repayment_description = "repayment_description" . $i;
            if (!empty($request->$amount && !empty($request->$loan_id))) {
                $loan = Loan::find($request->$loan_id);
                if ($request->$amount > GeneralHelper::loan_total_balance($loan->id)) {
                    Flash::warning("Amount is more than the balance(" . GeneralHelper::loan_total_balance($loan->id) . ')');
                    return redirect()->back()->withInput();

                } else {
                    $repayment = new LoanRepayment();
                    $repayment->user_id = Sentinel::getUser()->id;
                    $repayment->amount = $request->$amount;
                    $repayment->loan_id = $loan->id;
                    $repayment->borrower_id = $loan->borrower_id;
                    $repayment->collection_date = $request->$collected_date;
                    $repayment->repayment_method_id = $request->$repayment_method;
                    $repayment->notes = $request->$repayment_description;
                    $date = explode('-', $request->$collected_date);
                    $repayment->year = $date[0];
                    $repayment->month = $date[1];
                    //determine which schedule due date the payment applies too
                    $schedule = LoanSchedule::where('due_date', '>=', $request->$collected_date)->where('loan_id',
                        $loan->id)->orderBy('due_date',
                        'asc')->first();
                    if (!empty($schedule)) {
                        $repayment->due_date = $schedule->due_date;
                    } else {
                        $schedule = LoanSchedule::where('loan_id',
                            $loan->id)->orderBy('due_date',
                            'desc')->first();
                        if ($request->$collected_date > $schedule->due_date) {
                            $repayment->due_date = $schedule->due_date;
                        } else {
                            $schedule = LoanSchedule::where('due_date', '>',
                                $request->$collected_date)->where('loan_id',
                                $loan->id)->orderBy('due_date',
                                'asc')->first();
                            $repayment->due_date = $schedule->due_date;
                        }

                    }
                    $repayment->save();

                    //update loan status if need be
                    if ($request->$amount == GeneralHelper::loan_total_balance($loan->id)) {
                        $l = Loan::find($loan->id);
                        $l->loan_status = "fully_paid";
                        $l->save();

                    }
                    //check if late repayment is to be applied when adding payment

                    if (!empty($loan->loan_product)) {
                        if ($loan->loan_product->enable_late_repayment_penalty == 1) {
                            $schedules = LoanSchedule::where('due_date', '<',
                                $repayment->due_date)->where('missed_penalty_applied',
                                0)->orderBy('due_date', 'asc')->get();
                            foreach ($schedules as $schedule) {
                                if (GeneralHelper::loan_total_due_period($loan->id,
                                        $schedule->due_date) > GeneralHelper::loan_total_paid_period($loan->id,
                                        $schedule->due_date)
                                ) {
                                    $sch = LoanSchedule::find($schedule->id);
                                    $sch->missed_penalty_applied = 1;
                                    //determine which amount to use
                                    if ($loan->loan_product->late_repayment_penalty_type == "fixed") {
                                        $sch->penalty = $sch->penalty + $loan->loan_product->late_repayment_penalty_amount;
                                    } else {
                                        if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal') {
                                            $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'principal', $schedule->due_date));
                                            $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                        }
                                        if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal_interest') {
                                            $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                    $schedule->due_date) + GeneralHelper::loan_total_interest($loan->id,
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'principal',
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'interest', $schedule->due_date));
                                            $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                        }
                                        if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal_interest_fees') {
                                            $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                    $schedule->due_date) + GeneralHelper::loan_total_interest($loan->id,
                                                    $schedule->due_date) + GeneralHelper::loan_total_fees($loan->id,
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'principal',
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'interest',
                                                    $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                    'fees',
                                                    $schedule->due_date));
                                            $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                        }
                                        if ($loan->loan_product->late_repayment_penalty_calculate == 'total_overdue') {
                                            $principal = (GeneralHelper::loan_total_due_amount($loan->id,
                                                    $schedule->due_date) - GeneralHelper::loan_total_paid($loan->id,
                                                    $schedule->due_date));
                                            $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                        }
                                    }
                                    $sch->save();
                                }
                            }
                        }

                    }
                }
            }
            //notify borrower


        }
        Flash::success("Repayment successfully saved");
        return redirect('repayment/data');
    }

    public function createRepayment($loan)
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }
        $custom_fields = CustomField::where('category', 'repayments')->get();
        return view('loan_repayment.create', compact('loan', 'repayment_methods', 'custom_fields'));
    }

    public function storeRepayment(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('repayments.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        if ($request->amount > GeneralHelper::loan_total_balance($loan->id)) {
            Flash::warning("Amount is more than the balance(" . GeneralHelper::loan_total_balance($loan->id) . ')');
            return redirect()->back()->withInput();

        } else {
            $repayment = new LoanRepayment();
            $repayment->user_id = Sentinel::getUser()->id;
            $repayment->amount = $request->amount;
            $repayment->loan_id = $loan->id;
            $repayment->borrower_id = $loan->borrower_id;
            $repayment->collection_date = $request->collection_date;
            $repayment->repayment_method_id = $request->repayment_method_id;
            $repayment->notes = $request->notes;
            $date = explode('-', $request->collection_date);
            $repayment->year = $date[0];
            $repayment->month = $date[1];
            //determine which schedule due date the payment applies too
            $schedule = LoanSchedule::where('due_date', '>=', $request->collection_date)->where('loan_id',
                $loan->id)->orderBy('due_date',
                'asc')->first();
            if (!empty($schedule)) {
                $repayment->due_date = $schedule->due_date;
            } else {
                $schedule = LoanSchedule::where('loan_id',
                    $loan->id)->orderBy('due_date',
                    'desc')->first();
                if ($request->collection_date > $schedule->due_date) {
                    $repayment->due_date = $schedule->due_date;
                } else {
                    $schedule = LoanSchedule::where('due_date', '>', $request->collection_date)->where('loan_id',
                        $loan->id)->orderBy('due_date',
                        'asc')->first();
                    $repayment->due_date = $schedule->due_date;
                }

            }
            $repayment->save();
            //save custom meta
            $custom_fields = CustomField::where('category', 'repayments')->get();
            foreach ($custom_fields as $key) {
                $custom_field = new CustomFieldMeta();
                $id = $key->id;
                $custom_field->name = $request->$id;
                $custom_field->parent_id = $repayment->id;
                $custom_field->custom_field_id = $key->id;
                $custom_field->category = "repayments";
                $custom_field->save();
            }
            //update loan status if need be
            if ($request->amount == GeneralHelper::loan_total_balance($loan->id)) {
                $l = Loan::find($loan->id);
                $l->loan_status = "fully_paid";
                $l->save();

            }
            //check if late repayment is to be applied when adding payment
            if ($request->apply_penalty == 1) {
                if (!empty($loan->loan_product)) {
                    if ($loan->loan_product->enable_late_repayment_penalty == 1) {
                        $schedules = LoanSchedule::where('due_date', '<',
                            $repayment->due_date)->where('missed_penalty_applied',
                            0)->orderBy('due_date', 'asc')->get();
                        foreach ($schedules as $schedule) {
                            if (GeneralHelper::loan_total_due_period($loan->id,
                                    $schedule->due_date) > GeneralHelper::loan_total_paid_period($loan->id,
                                    $schedule->due_date)
                            ) {
                                $sch = LoanSchedule::find($schedule->id);
                                $sch->missed_penalty_applied = 1;
                                //determine which amount to use
                                if ($loan->loan_product->late_repayment_penalty_type == "fixed") {
                                    $sch->penalty = $sch->penalty + $loan->loan_product->late_repayment_penalty_amount;
                                } else {
                                    if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal') {
                                        $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                'principal', $schedule->due_date));
                                        $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                    }
                                    if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal_interest') {
                                        $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                $schedule->due_date) + GeneralHelper::loan_total_interest($loan->id,
                                                $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                'principal',
                                                $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                'interest', $schedule->due_date));
                                        $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                    }
                                    if ($loan->loan_product->late_repayment_penalty_calculate == 'overdue_principal_interest_fees') {
                                        $principal = (GeneralHelper::loan_total_principal($loan->id,
                                                $schedule->due_date) + GeneralHelper::loan_total_interest($loan->id,
                                                $schedule->due_date) + GeneralHelper::loan_total_fees($loan->id,
                                                $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                'principal',
                                                $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id,
                                                'interest',
                                                $schedule->due_date) - GeneralHelper::loan_paid_item($loan->id, 'fees',
                                                $schedule->due_date));
                                        $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                    }
                                    if ($loan->loan_product->late_repayment_penalty_calculate == 'total_overdue') {
                                        $principal = (GeneralHelper::loan_total_due_amount($loan->id,
                                                $schedule->due_date) - GeneralHelper::loan_total_paid($loan->id,
                                                $schedule->due_date));
                                        $sch->penalty = $sch->penalty + (($loan->loan_product->late_repayment_penalty_amount / 100) * $principal);
                                    }
                                }
                                $sch->save();
                            }
                        }
                    }
                }
            }
            //notify borrower
            if ($request->notify_borrower == 1) {
                if ($request->notify_method == 'both') {
                    $borrower = $loan->borrower;
                    //sent via email
                    if (!empty($borrower->email)) {
                        $body = Setting::where('setting_key',
                            'payment_received_email_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                        $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
                        $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                        $body = str_replace('{loanNumber}', '#' . $loan->id, $body);
                        $body = str_replace('{paymentAmount}', $request->amount, $body);
                        $body = str_replace('{paymentDate}', $request->date, $body);
                        $body = str_replace('{loanAmount}', $loan->principal, $body);
                        $body = str_replace('{loanDue}',
                            round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                        $body = str_replace('{loanBalance}',
                            round(GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id),
                                2), $body);
                        $body = str_replace('{loansDue}',
                            round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                        $body = str_replace('{loansBalance}',
                            round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                                2), $body);
                        $body = str_replace('{loansPayments}',
                            GeneralHelper::borrower_loans_total_paid($borrower->id),
                            $body);
                        PDF::AddPage();
                        PDF::writeHTML(View::make('loan_repayment.pdf', compact('loan', 'repayment'))->render());
                        PDF::SetAuthor('Tererai Mugova');
                        PDF::Output(public_path() . '/uploads/temporary/repayment_receipt' . $loan->id . ".pdf", 'F');
                        $file_name = $loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Repayment Receipt.pdf";
                        Mail::raw($body, function ($message) use ($loan, $request, $borrower, $file_name) {
                            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                                Setting::where('setting_key', 'company_name')->first()->setting_value);
                            $message->to($borrower->email);
                            $headers = $message->getHeaders();
                            $message->attach(public_path() . '/uploads/temporary/repayment_receipt' . $loan->id . ".pdf",
                                ["as" => $file_name]);
                            $message->setContentType('text/html');
                            $message->setSubject(Setting::where('setting_key',
                                'payment_received_email_subject')->first()->setting_value);

                        });
                        unlink(public_path() . '/uploads/temporary/repayment_receipt' . $loan->id . ".pdf");
                        $mail = new Email();
                        $mail->user_id = Sentinel::getUser()->id;
                        $mail->message = $body;
                        $mail->subject = $request->subject;
                        $mail->recipients = 1;
                        $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                        $mail->save();
                    }
                    if (!empty($borrower->mobile)) {
                        if (Setting::where('setting_key', 'sms_enabled')->first()->setting_value == 1) {
                            //lets build and replace available tags
                            $body = Setting::where('setting_key',
                                'payment_received_sms_template')->first()->setting_value;
                            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                            $body = str_replace('{loanNumber}', '#' . $loan->id, $body);
                            $body = str_replace('{paymentAmount}', $request->amount, $body);
                            $body = str_replace('{paymentDate}', $request->date, $body);
                            $body = str_replace('{loanAmount}', $loan->principal, $body);
                            $body = str_replace('{loanTotalDue}',
                                round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                            $body = str_replace('{loanBalance}',
                                round(GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id),
                                    2), $body);
                            $body = str_replace('{lLoansDue}',
                                round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                            $body = str_replace('{loansBalance}',
                                round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                                    2), $body);
                            $body = str_replace('{loansPayments}',
                                GeneralHelper::borrower_loans_total_paid($borrower->id),
                                $body);
                            $body = trim(strip_tags($body));
                            if (!empty($borrower->mobile)) {
                                $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                                if ($active_sms == 'twilio') {
                                    $twilio = new Twilio(Setting::where('setting_key',
                                        'twilio_sid')->first()->setting_value,
                                        Setting::where('setting_key', 'twilio_token')->first()->setting_value,
                                        Setting::where('setting_key', 'twilio_phone_number')->first()->setting_value);
                                    $twilio->message('+' . $borrower->mobile, $body);
                                }
                                if ($active_sms == 'routesms') {
                                    $host = Setting::where('setting_key', 'routesms_host')->first()->setting_value;
                                    $port = Setting::where('setting_key', 'routesms_port')->first()->setting_value;
                                    $username = Setting::where('setting_key',
                                        'routesms_username')->first()->setting_value;
                                    $password = Setting::where('setting_key',
                                        'routesms_password')->first()->setting_value;
                                    $sender = Setting::where('setting_key', 'sms_sender')->first()->setting_value;
                                    $SMSText = $body;
                                    $GSM = $borrower->mobile;
                                    $msgtype = 2;
                                    $dlr = 1;
                                    $routesms = new RouteSms($host, $port, $username, $password, $sender, $SMSText,
                                        $GSM, $msgtype,
                                        $dlr);
                                    $routesms->Submit();
                                }
                                if ($active_sms == 'clickatell') {
                                    $clickatell = new ClickatellHttp(Setting::where('setting_key',
                                        'clickatell_username')->first()->setting_value,
                                        Setting::where('setting_key', 'clickatell_password')->first()->setting_value,
                                        Setting::where('setting_key', 'clickatell_api_id')->first()->setting_value);
                                    $response = $clickatell->sendMessage(array($borrower->mobile), $body);
                                }
                                if ($active_sms == 'infobip') {
                                    $infobip = new Infobip(Setting::where('setting_key',
                                        'sms_sender')->first()->setting_value, $body,
                                        $borrower->mobile);
                                }
                                $sms = new Sms();
                                $sms->user_id = Sentinel::getUser()->id;
                                $sms->message = $body;
                                $sms->gateway = $active_sms;
                                $sms->recipients = 1;
                                $sms->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                                $sms->save();

                            }

                        }
                    }
                    //send via sms
                }
                if ($request->notify_method == 'email') {
                    $borrower = $loan->borrower;
                    //sent via email
                    if (!empty($borrower->email)) {
                        $body = Setting::where('setting_key',
                            'payment_received_email_template')->first()->setting_value;
                        $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                        $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                        $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                        $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                        $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
                        $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                        $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
                        $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                        $body = str_replace('{loanNumber}', '#' . $loan->id, $body);
                        $body = str_replace('{paymentAmount}', $request->amount, $body);
                        $body = str_replace('{paymentDate}', $request->date, $body);
                        $body = str_replace('{loanAmount}', $loan->principal, $body);
                        $body = str_replace('{loanDue}',
                            round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                        $body = str_replace('{loanBalance}',
                            round(GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id),
                                2), $body);
                        $body = str_replace('{loansDue}',
                            round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                        $body = str_replace('{loansBalance}',
                            round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                                2), $body);
                        $body = str_replace('{loansPayments}',
                            GeneralHelper::borrower_loans_total_paid($borrower->id),
                            $body);
                        PDF::AddPage();
                        PDF::writeHTML(View::make('loan_repayment.pdf', compact('loan', 'repayment'))->render());
                        PDF::SetAuthor('Tererai Mugova');
                        PDF::Output(public_path() . '/uploads/temporary/repayment_receipt' . $loan->id . ".pdf", 'F');
                        $file_name = $loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Repayment Receipt.pdf";
                        Mail::raw($body, function ($message) use ($loan, $request, $borrower, $file_name) {
                            $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                                Setting::where('setting_key', 'company_name')->first()->setting_value);
                            $message->to($borrower->email);
                            $headers = $message->getHeaders();
                            $message->attach(public_path() . '/uploads/temporary/repayment_receipt' . $loan->id . ".pdf",
                                ["as" => $file_name]);
                            $message->setContentType('text/html');
                            $message->setSubject(Setting::where('setting_key',
                                'payment_received_email_subject')->first()->setting_value);

                        });
                        unlink(public_path() . '/uploads/temporary/repayment_receipt' . $loan->id . ".pdf");
                        $mail = new Email();
                        $mail->user_id = Sentinel::getUser()->id;
                        $mail->message = $body;
                        $mail->subject = $request->subject;
                        $mail->recipients = 1;
                        $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                        $mail->save();
                    }
                }
                if ($request->notify_method == 'sms') {
                    $borrower = $loan->borrower;
                    if (!empty($borrower->mobile)) {
                        if (Setting::where('setting_key', 'sms_enabled')->first()->setting_value == 1) {
                            //lets build and replace available tags
                            $body = Setting::where('setting_key',
                                'payment_received_sms_template')->first()->setting_value;
                            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                            $body = str_replace('{loanNumber}', '#' . $loan->id, $body);
                            $body = str_replace('{paymentAmount}', $request->amount, $body);
                            $body = str_replace('{paymentDate}', $request->date, $body);
                            $body = str_replace('{loanAmount}', $loan->principal, $body);
                            $body = str_replace('{loanTotalDue}',
                                round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
                            $body = str_replace('{loanBalance}',
                                round(GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id),
                                    2), $body);
                            $body = str_replace('{lLoansDue}',
                                round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                            $body = str_replace('{loansBalance}',
                                round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                                    2), $body);
                            $body = str_replace('{loansPayments}',
                                GeneralHelper::borrower_loans_total_paid($borrower->id),
                                $body);
                            $body = trim(strip_tags($body));
                            if (!empty($borrower->mobile)) {
                                $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                                if ($active_sms == 'twilio') {
                                    $twilio = new Twilio(Setting::where('setting_key',
                                        'twilio_sid')->first()->setting_value,
                                        Setting::where('setting_key', 'twilio_token')->first()->setting_value,
                                        Setting::where('setting_key', 'twilio_phone_number')->first()->setting_value);
                                    $twilio->message('+' . $borrower->mobile, $body);
                                }
                                if ($active_sms == 'routesms') {
                                    $host = Setting::where('setting_key', 'routesms_host')->first()->setting_value;
                                    $port = Setting::where('setting_key', 'routesms_port')->first()->setting_value;
                                    $username = Setting::where('setting_key',
                                        'routesms_username')->first()->setting_value;
                                    $password = Setting::where('setting_key',
                                        'routesms_password')->first()->setting_value;
                                    $sender = Setting::where('setting_key', 'sms_sender')->first()->setting_value;
                                    $SMSText = $body;
                                    $GSM = $borrower->mobile;
                                    $msgtype = 2;
                                    $dlr = 1;
                                    $routesms = new RouteSms($host, $port, $username, $password, $sender, $SMSText,
                                        $GSM, $msgtype,
                                        $dlr);
                                    $routesms->Submit();
                                }
                                if ($active_sms == 'clickatell') {
                                    $clickatell = new ClickatellHttp(Setting::where('setting_key',
                                        'clickatell_username')->first()->setting_value,
                                        Setting::where('setting_key', 'clickatell_password')->first()->setting_value,
                                        Setting::where('setting_key', 'clickatell_api_id')->first()->setting_value);
                                    $response = $clickatell->sendMessage(array($borrower->mobile), $body);
                                }
                                if ($active_sms == 'infobip') {
                                    $infobip = new Infobip(Setting::where('setting_key',
                                        'sms_sender')->first()->setting_value, $body,
                                        $borrower->mobile);
                                }
                                $sms = new Sms();
                                $sms->user_id = Sentinel::getUser()->id;
                                $sms->message = $body;
                                $sms->gateway = $active_sms;
                                $sms->recipients = 1;
                                $sms->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                                $sms->save();

                            }

                        }
                    }
                }
            }
            Flash::success("Repayment successfully saved");
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    public function deleteRepayment($loan, $id)
    {
        if (!Sentinel::hasAccess('repayments.delete')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        LoanRepayment::destroy($id);
        if (GeneralHelper::loan_total_balance($loan->id) > 0 && $loan->loan_status == "full_paid") {
            $l = Loan::find($loan->id);
            $l->loan_status = "open";
            $l->save();
        }
        Flash::success("Repayment successfully deleted");
        return redirect('loan/' . $loan->id . '/show');
    }

//    print repayment
    public function pdfRepayment($loan, $repayment)
    {
        PDF::AddPage();
        PDF::writeHTML(View::make('loan_repayment.pdf', compact('loan', 'repayment'))->render());
        PDF::SetAuthor('Tererai Mugova');
        PDF::Output($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Repayment Receipt.pdf",
            'D');
    }

    public function printRepayment($loan, $repayment)
    {

        return view('loan_repayment.print', compact('loan', 'repayment'));
    }

    public function editRepayment($loan, $repayment)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }
        $custom_fields = CustomField::where('category', 'repayments')->get();
        return view('loan_repayment.edit', compact('loan', 'repayment_methods', 'custom_fields', 'repayment'));
    }

    public function updateRepayment(Request $request, $loan, $id)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        if ($request->amount > GeneralHelper::loan_total_balance($loan->id)) {
            Flash::warning("Amount is more than the balance(" . GeneralHelper::loan_total_balance($loan->id) . ')');
            return redirect()->back()->withInput();

        } else {
            $repayment = LoanRepayment::find($id);
            $repayment->amount = $request->amount;
            $repayment->loan_id = $loan->id;
            $repayment->collection_date = $request->collection_date;
            $repayment->repayment_method_id = $request->repayment_method_id;
            $repayment->notes = $request->notes;
            $date = explode('-', $request->collection_date);
            $repayment->year = $date[0];
            $repayment->month = $date[1];
            //determine which schedule due date the payment applies too
            $schedule = LoanSchedule::where('due_date', '>=', $request->collection_date)->where('loan_id',
                $loan->id)->orderBy('due_date',
                'asc')->first();
            if (!empty($schedule)) {
                $repayment->due_date = $schedule->due_date;
            } else {
                $schedule = LoanSchedule::where('loan_id',
                    $loan->id)->orderBy('due_date',
                    'desc')->first();
                if ($request->collection_date > $schedule->due_date) {
                    $repayment->due_date = $schedule->due_date;
                } else {
                    $schedule = LoanSchedule::where('due_date', '>', $request->collection_date)->where('loan_id',
                        $loan->id)->orderBy('due_date',
                        'asc')->first();
                    $repayment->due_date = $schedule->due_date;
                }

            }
            $repayment->save();
            //save custom meta
            $custom_fields = CustomField::where('category', 'repayments')->get();
            foreach ($custom_fields as $key) {
                if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'repayments')->first())
                ) {
                    $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                        $id)->where('category', 'repayments')->first();
                } else {
                    $custom_field = new CustomFieldMeta();
                }
                $kid = $key->id;
                $custom_field->name = $request->$kid;
                $custom_field->parent_id = $id;
                $custom_field->custom_field_id = $key->id;
                $custom_field->category = "repayments";
                $custom_field->save();
            }
            //update loan status if need be
            if ($request->amount == GeneralHelper::loan_total_balance($loan->id)) {
                $l = Loan::find($loan->id);
                $l->loan_status = "fully_paid";
                $l->save();

            }
            Flash::success("Repayment successfully saved");
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    //edit loan schedule
    public function editSchedule($loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $rows = 0;
        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        return view('loan.edit_schedule', compact('loan', 'schedules', 'rows'));
    }

    public function updateSchedule(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        if ($request->submit == 'add_row') {
            $rows = $request->addrows;
            $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
            return view('loan.edit_schedule', compact('loan', 'schedules', 'rows'));
        }
        if ($request->submit == 'submit') {
            //lets delete existing schedules
            LoanSchedule::where('loan_id', $loan->id)->delete();
            for ($count = 0; $count < $request->count; $count++) {
                $schedule = new LoanSchedule();
                if (empty($request->due_date) && empty($request->principal) && empty($request->interest) && empty($request->fees) && empty($request->penalty)) {
                    //do nothing
                } elseif (empty($request->due_date)) {
                    //do nothing
                } else {
                    //all rosy, lets save our data here
                    $schedule->due_date = $request->due_date[$count];
                    $schedule->principal = $request->principal[$count];
                    $schedule->description = $request->description[$count];
                    $schedule->loan_id = $loan->id;
                    $schedule->borrower_id = $loan->borrower_id;
                    $schedule->interest = $request->interest[$count];
                    $schedule->fees = $request->fees[$count];
                    $schedule->penalty = $request->penalty[$count];
                    $date = explode('-', $request->due_date[$count]);
                    $schedule->month = $date[1];
                    $schedule->year = $date[0];
                    $schedule->save();
                }
            }
            Flash::success("Schedule successfully updated");
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    public function pdfSchedule($loan)
    {

        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        PDF::AddPage();
        PDF::writeHTML(View::make('loan.pdf_schedule', compact('loan', 'schedules'))->render());
        PDF::SetAuthor('Tererai Mugova');
        PDF::Output($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Loan Repayment Schedule.pdf",
            'D');

    }

    public function printSchedule($loan)
    {
        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        return view('loan.print_schedule', compact('loan', 'schedules'));
    }

    public function pdfLoanStatement($loan)
    {
        $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
        PDF::AddPage();
        PDF::writeHTML(View::make('loan.pdf_loan_statement', compact('loan', 'payments'))->render());
        PDF::SetAuthor('Tererai Mugova');
        PDF::Output($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Loan Statement.pdf",
            'D');
    }

    public function printLoanStatement($loan)
    {
        $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
        return view('loan.print_loan_statement', compact('loan', 'payments'));
    }

    public function pdfBorrowerStatement($borrower)
    {
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        PDF::AddPage();
        PDF::writeHTML(View::make('loan.pdf_borrower_statement', compact('loans'))->render());
        PDF::SetAuthor('Tererai Mugova');
        PDF::Output($borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Client Statement.pdf",
            'D');
    }

    public function printBorrowerStatement($borrower)
    {
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        return view('loan.print_borrower_statement', compact('loans'));
    }

    public function override(Request $request, $loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        if ($request->isMethod('post')) {
            $l = Loan::find($loan->id);
            $l->balance = $request->balance;
            if (empty($request->override)) {
                $l->override = 0;
            } else {
                $l->override = $request->override;
            }
            $l->save();
            Flash::success(trans('general.override_successfully_applied'));
            return redirect('loan/' . $loan->id . '/show');
        }
        return view('loan.override', compact('loan'));
    }

    public function emailBorrowerStatement($borrower)
    {
        if (!empty($borrower->email)) {
            $body = Setting::where('setting_key',
                'borrower_statement_email_template')->first()->setting_value;
            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
            $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
            $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
            $body = str_replace('{loansPayments}', GeneralHelper::borrower_loans_total_paid($borrower->id), $body);
            $body = str_replace('{loansDue}',
                round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
            $body = str_replace('{loansBalance}',
                round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                    2), $body);
            $body = str_replace('{loanPayments}',
                GeneralHelper::borrower_loans_total_paid($borrower->id),
                $body);
            $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
            PDF::AddPage();
            PDF::writeHTML(View::make('loan.pdf_borrower_statement', compact('loans'))->render());
            PDF::SetAuthor('Tererai Mugova');
            PDF::Output(public_path() . '/uploads/temporary/borrower_statement' . $borrower->id . ".pdf", 'F');
            $file_name = $borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Client Statement.pdf";
            Mail::raw($body, function ($message) use ($borrower, $file_name) {
                $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                    Setting::where('setting_key', 'company_name')->first()->setting_value);
                $message->to($borrower->email);
                $headers = $message->getHeaders();
                $message->attach(public_path() . '/uploads/temporary/borrower_statement' . $borrower->id . ".pdf",
                    ["as" => $file_name]);
                $message->setContentType('text/html');
                $message->setSubject(Setting::where('setting_key',
                    'borrower_statement_email_subject')->first()->setting_value);

            });
            unlink(public_path() . '/uploads/temporary/borrower_statement' . $borrower->id . ".pdf");
            $mail = new Email();
            $mail->user_id = Sentinel::getUser()->id;
            $mail->message = $body;
            $mail->subject = Setting::where('setting_key',
                'borrower_statement_email_subject')->first()->setting_value;
            $mail->recipients = 1;
            $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
            $mail->save();
            Flash::success("Statment successfully sent");
            return redirect('borrower/' . $borrower->id . '/show');
        } else {
            Flash::warning("Borrower has no email set");
            return redirect('borrower/' . $borrower->id . '/show');
        }
    }

    public function emailLoanStatement($loan)
    {
        $borrower = $loan->borrower;
        if (!empty($borrower->email)) {
            $body = Setting::where('setting_key',
                'loan_statement_email_template')->first()->setting_value;
            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
            $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
            $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
            $body = str_replace('{loanNumber}', $loan->id, $body);
            $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
            $body = str_replace('{loanDue}',
                round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
            $body = str_replace('{loanBalance}',
                round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                    2), $body);
            $payments = LoanRepayment::where('loan_id', $loan->id)->orderBy('collection_date', 'asc')->get();
            PDF::AddPage();
            PDF::writeHTML(View::make('loan.pdf_loan_statement', compact('loan', 'payments'))->render());
            PDF::SetAuthor('Tererai Mugova');
            PDF::Output(public_path() . '/uploads/temporary/loan_statement' . $loan->id . ".pdf", 'F');
            $file_name = $borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Loan Statement.pdf";
            Mail::raw($body, function ($message) use ($borrower, $loan, $file_name) {
                $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                    Setting::where('setting_key', 'company_name')->first()->setting_value);
                $message->to($borrower->email);
                $headers = $message->getHeaders();
                $message->attach(public_path() . '/uploads/temporary/loan_statement' . $loan->id . ".pdf",
                    ["as" => $file_name]);
                $message->setContentType('text/html');
                $message->setSubject(Setting::where('setting_key',
                    'loan_statement_email_subject')->first()->setting_value);

            });
            unlink(public_path() . '/uploads/temporary/loan_statement' . $loan->id . ".pdf");
            $mail = new Email();
            $mail->user_id = Sentinel::getUser()->id;
            $mail->message = $body;
            $mail->subject = Setting::where('setting_key',
                'loan_statement_email_subject')->first()->setting_value;
            $mail->recipients = 1;
            $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
            $mail->save();
            Flash::success("Loan Statement successfully sent");
            return redirect('loan/' . $loan->id . '/show');
        } else {
            Flash::warning("Borrower has no email set");
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    public function emailLoanSchedule($loan)
    {
        $borrower = $loan->borrower;
        if (!empty($borrower->email)) {
            $body = Setting::where('setting_key',
                'loan_schedule_email_template')->first()->setting_value;
            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
            $body = str_replace('{borrowerUniqueNumber}', $borrower->unique_number, $body);
            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
            $body = str_replace('{borrowerPhone}', $borrower->phone, $body);
            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
            $body = str_replace('{loanNumber}', $loan->id, $body);
            $body = str_replace('{loanPayments}', GeneralHelper::loan_total_paid($loan->id), $body);
            $body = str_replace('{loanDue}',
                round(GeneralHelper::loan_total_due_amount($loan->id), 2), $body);
            $body = str_replace('{loanBalance}',
                round((GeneralHelper::loan_total_due_amount($loan->id) - GeneralHelper::loan_total_paid($loan->id)),
                    2), $body);
            $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
            PDF::AddPage();
            PDF::writeHTML(View::make('loan.pdf_schedule', compact('loan', 'schedules'))->render());
            PDF::SetAuthor('Tererai Mugova');
            PDF::Output(public_path() . '/uploads/temporary/loan_schedule' . $loan->id . ".pdf", 'F');
            $file_name = $borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Loan Schedule.pdf";
            Mail::raw($body, function ($message) use ($borrower, $loan, $file_name) {
                $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                    Setting::where('setting_key', 'company_name')->first()->setting_value);
                $message->to($borrower->email);
                $headers = $message->getHeaders();
                $message->attach(public_path() . '/uploads/temporary/loan_schedule' . $loan->id . ".pdf",
                    ["as" => $file_name]);
                $message->setContentType('text/html');
                $message->setSubject(Setting::where('setting_key',
                    'loan_schedule_email_subject')->first()->setting_value);

            });
            unlink(public_path() . '/uploads/temporary/loan_schedule' . $loan->id . ".pdf");
            $mail = new Email();
            $mail->user_id = Sentinel::getUser()->id;
            $mail->message = $body;
            $mail->subject = Setting::where('setting_key',
                'loan_statement_email_subject')->first()->setting_value;
            $mail->recipients = 1;
            $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
            $mail->save();
            Flash::success("Loan Statement successfully sent");
            return redirect('loan/' . $loan->id . '/show');
        } else {
            Flash::warning("Borrower has no email set");
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    //loan applications
    public function indexApplication()
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $data = LoanApplication::all();

        return view('loan.applications', compact('data'));
    }

    public function declineApplication($id)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $application = LoanApplication::find($id);
        $application->status = "declined";
        $application->save();
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect('loan/loan_application/data');
    }

    public function deleteApplication($id)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        LoanApplication::destroy($id);
        Flash::success(trans_choice('general.successfully_deleted', 1));
        return redirect('loan/loan_application/data');
    }

    public function approveApplication($id)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $loan_disbursed_by = array();
        foreach (LoanDisbursedBy::all() as $key) {
            $loan_disbursed_by[$key->id] = $key->name;
        }
        $application = LoanApplication::find($id);
        //get custom fields
        $custom_fields = CustomField::where('category', 'loans')->get();
        $loan_fees = LoanFee::all();
        $loan_product = $application->loan_product;
        if (!empty($application->loan_product)) {
            return view('loan.approve_application',
                compact('id', 'application', 'loan_product', 'loan_disbursed_by', 'custom_fields', 'loan_fees'));

        } else {
            Flash::warning(trans_choice('general.loan_application_approve_error', 1));
            return redirect('loan/loan_application/data');
        }
    }

    public function storeApproveApplication(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $application = LoanApplication::find($id);
        $application->status = "approved";
        $application->save();
        //lets save the loan here
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $loan = new Loan();
        $loan->loan_disbursed_by_id = $request->loan_disbursed_by_id;
        $loan->principal = $request->principal;
        $loan->interest_method = $request->interest_method;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_period = $request->interest_period;
        $loan->loan_duration = $request->loan_duration;
        $loan->loan_duration_type = $request->loan_duration_type;
        $loan->repayment_cycle = $request->repayment_cycle;
        $loan->decimal_places = $request->decimal_places;
        $loan->repayment_order = $request->repayment_order;
        $loan->override_interest = $request->override_interest;
        $loan->override_interest_amount = $request->override_interest_amount;
        $loan->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan->borrower_id = $request->borrower_id;
        $loan->user_id = Sentinel::getUser()->id;
        $loan->loan_product_id = $request->loan_product_id;
        $loan->release_date = $request->release_date;
        $date = explode('-', $request->release_date);
        $loan->month = $date[1];
        $loan->year = $date[0];
        $loan->first_payment_date = $request->first_payment_date;
        $loan->description = $request->description;
        $files = array();
        if (!empty(array_filter($request->file('files')))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $files[$count] = $key->getClientOriginalName();
                    $key->move(public_path() . '/uploads',
                        $key->getClientOriginalName());
                }
                $count++;
            }
        }
        $loan->files = serialize($files);
        $loan->save();

        //save custom meta
        $custom_fields = CustomField::where('category', 'loans')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $loan->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "loans";
            $custom_field->save();
        }
        //save loan fees
        $fees_distribute = 0;
        $fees_first_payment = 0;
        $fees_last_payment = 0;
        foreach (LoanFee::all() as $key) {
            $loan_fee = new LoanFeeMeta();
            $value = 'loan_fees_amount_' . $key->id;
            $loan_fees_schedule = 'loan_fees_schedule_' . $key->id;
            $loan_fee->user_id = Sentinel::getUser()->id;
            $loan_fee->category = 'loan';
            $loan_fee->parent_id = $loan->id;
            $loan_fee->loan_fees_id = $key->id;
            $loan_fee->value = $request->$value;
            $loan_fee->loan_fees_schedule = $request->$loan_fees_schedule;
            $loan_fee->save();
            //determine amount to use
            if ($key->loan_fee_type == 'fixed') {
                if ($loan_fee->loan_fees_schedule == 'distribute_fees_evenly') {
                    $fees_distribute = $fees_distribute + $loan_fee->value;
                }
                if ($loan_fee->loan_fees_schedule == 'charge_fees_on_first_payment') {
                    $fees_first_payment = $fees_first_payment + $loan_fee->value;
                }
                if ($loan_fee->loan_fees_schedule == 'charge_fees_on_last_payment') {
                    $fees_last_payment = $fees_last_payment + $loan_fee->value;
                }
            } else {
                if ($loan_fee->loan_fees_schedule == 'distribute_fees_evenly') {
                    $fees_distribute = $fees_distribute + ($loan_fee->value * $loan->principal / 100);
                }
                if ($loan_fee->loan_fees_schedule == 'charge_fees_on_first_payment') {
                    $fees_first_payment = $fees_first_payment + ($loan_fee->value * $loan->principal / 100);
                }
                if ($loan_fee->loan_fees_schedule == 'charge_fees_on_last_payment') {
                    $fees_last_payment = $fees_last_payment + ($loan_fee->value * $loan->principal / 100);
                }
            }
        }
        //lets create schedules here
        //determine interest rate to use

        $interest_rate = GeneralHelper::determine_interest_rate($loan->id);

        $period = GeneralHelper::loan_period($loan->id);
        $loan = Loan::find($loan->id);
        if ($loan->repayment_cycle == 'daily') {
            $repayment_cycle = 'day';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' days')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'weekly') {
            $repayment_cycle = 'week';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' weeks')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'monthly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'bi_monthly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'quarterly') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'semi_annually') {
            $repayment_cycle = 'month';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' months')),
                'Y-m-d');
        }
        if ($loan->repayment_cycle == 'yearly') {
            $repayment_cycle = 'year';
            $loan->maturity_date = date_format(date_add(date_create($request->first_payment_date),
                date_interval_create_from_date_string($period . ' years')),
                'Y-m-d');
        }
        $loan->save();
        //generate schedules until period finished
        $next_payment = $request->first_payment_date;
        $balance = $request->principal;
        for ($i = 1; $i <= $period; $i++) {
            $fees = 0;
            if ($i == 1) {
                $fees = $fees + ($fees_first_payment);
            }
            if ($i == $period) {
                $fees = $fees + ($fees_last_payment);
            }
            $fees = $fees + ($fees_distribute / $period);
            $loan_schedule = new LoanSchedule();
            $loan_schedule->loan_id = $loan->id;
            $loan_schedule->fees = $fees;
            $loan_schedule->borrower_id = $loan->borrower_id;
            $loan_schedule->description = 'Repayment';
            $loan_schedule->due_date = $next_payment;
            $date = explode('-', $next_payment);
            $loan_schedule->month = $date[1];
            $loan_schedule->year = $date[0];
            //determine which method to use
            $due = 0;
            //reducing balance equal installments
            if ($request->interest_method == 'declining_balance_equal_installments') {
                $due = GeneralHelper::amortized_monthly_payment($loan->id, $loan->principal);
                if ($loan->decimal_places == 'round_off_to_two_decimal') {
                    //determine if we have grace period for interest

                    $interest = round(($interest_rate * $balance), 2);
                    $loan_schedule->principal = round(($due - $interest), 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest, 2);
                    }
                    $loan_schedule->due = round($due, 2);
                    //determine next balance
                    $balance = round(($balance - ($due - $interest)), 2);
                    $loan_schedule->principal_balance = round($balance, 2);
                } else {
                    //determine if we have grace period for interest

                    $interest = round(($interest_rate * $balance));
                    $loan_schedule->principal = round(($due - $interest));
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest);
                    }
                    $loan_schedule->due = round($due);
                    //determine next balance
                    $balance = round(($balance - ($due - $interest)));
                    $loan_schedule->principal_balance = round($balance);
                }


            }
            //reducing balance equal principle
            if ($request->interest_method == 'declining_balance_equal_principal') {
                $principal = $loan->principal / $period;
                if ($loan->decimal_places == 'round_off_to_two_decimal') {

                    $interest = round(($interest_rate * $balance), 2);
                    $loan_schedule->principal = round($principal, 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest, 2);
                    }
                    $loan_schedule->due = round(($principal + $interest), 2);
                    //determine next balance
                    $balance = round(($balance - ($principal + $interest)), 2);
                    $loan_schedule->principal_balance = round($balance, 2);
                } else {

                    $loan_schedule->principal = round(($principal));

                    $interest = round(($interest_rate * $balance));
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest);
                    }
                    $loan_schedule->due = round($principal + $interest);
                    //determine next balance
                    $balance = round(($balance - ($principal + $interest)));
                    $loan_schedule->principal_balance = round($balance);
                }

            }
            //flat  method
            if ($request->interest_method == 'flat_rate') {
                $principal = $loan->principal / $period;
                if ($loan->decimal_places == 'round_off_to_two_decimal') {
                    $interest = round(($interest_rate * $loan->principal), 2);
                    $loan_schedule->principal = round(($principal), 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest, 2);
                    }
                    $loan_schedule->principal = round(($principal), 2);
                    $loan_schedule->due = round(($principal + $interest), 2);
                    //determine next balance
                    $balance = round(($balance - $principal), 2);
                    $loan_schedule->principal_balance = round($balance, 2);
                } else {
                    $interest = round(($interest_rate * $loan->principal));
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest);
                    }
                    $loan_schedule->principal = round($principal);
                    $loan_schedule->due = round($principal + $interest);
                    //determine next balance
                    $balance = round(($balance - $principal));
                    $loan_schedule->principal_balance = round($balance);
                }
            }
            //interest only method
            if ($request->interest_method == 'interest_only') {
                if ($i == $period) {
                    $principal = $loan->principal;
                } else {
                    $principal = 0;
                }
                if ($loan->decimal_places == 'round_off_to_two_decimal') {
                    $interest = round(($interest_rate * $loan->principal), 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest, 2);
                    }
                    $loan_schedule->principal = round(($principal), 2);
                    $loan_schedule->due = round(($principal + $interest), 2);
                    //determine next balance
                    $balance = round(($balance - $principal), 2);
                    $loan_schedule->principal_balance = round($balance, 2);
                } else {
                    $interest = round(($interest_rate * $loan->principal));
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_schedule->interest = 0;
                    } else {
                        $loan_schedule->interest = round($interest);
                    }
                    $loan_schedule->principal = round($principal);
                    $loan_schedule->due = round($principal + $interest);
                    //determine next balance
                    $balance = round(($balance - $principal));
                    $loan_schedule->principal_balance = round($balance);
                }
            }
            //determine next due date
            if ($loan->repayment_cycle == 'daily') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 days')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'weekly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 weeks')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'monthly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 months')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'bi_monthly') {
                $next_payment = date_format(date_add(date_create($request->first_payment_date),
                    date_interval_create_from_date_string($period . ' months')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'quarterly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('2 months')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'semi_annually') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('6 months')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($loan->repayment_cycle == 'yearly') {
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string('1 years')),
                    'Y-m-d');
                $loan_schedule->due_date = $next_payment;
            }
            if ($i == $period) {
                $loan_schedule->principal_balance = round($balance);
            }
            $loan_schedule->save();
        }
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect('loan/loan_application/data');
    }
}
