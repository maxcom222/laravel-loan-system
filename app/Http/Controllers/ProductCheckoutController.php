<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;

use App\Models\Borrower;
use App\Models\Branch;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanDisbursedBy;
use App\Models\LoanFee;
use App\Models\LoanFeeMeta;
use App\Models\LoanProduct;
use App\Models\LoanProductCharge;
use App\Models\LoanRepaymentMethod;
use App\Models\Product;
use App\Models\ProductCheckin;
use App\Models\ProductCheckout;
use App\Models\ProductCheckinItem;
use App\Models\ProductCheckoutItem;
use App\Models\ProductPayment;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class ProductCheckoutController extends Controller
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
        if (!Sentinel::hasAccess('stock')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = ProductCheckout::all();

        return view('check_out.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!Sentinel::hasAccess('stock.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $warehouses = array();
        foreach (Warehouse::all() as $key) {
            $warehouses[$key->id] = $key->name;
        }
        $suppliers = array();
        foreach (Supplier::all() as $key) {
            $suppliers[$key->id] = $key->name;
        }
        $products = array();
        foreach (Product::all() as $key) {
            $products[$key->id] = $key->name;
        }
        $payment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $payment_methods[$key->id] = $key->name;
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
        if (empty($loan_product)) {
            Flash::warning("No loan product set. You must first set a loan product");
            return redirect()->back();
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'products_check_out')->get();
        $l_custom_fields = CustomField::where('category', 'loans')->get();
        $charges = array();
        foreach (LoanProductCharge::where('loan_product_id', $loan_product->id)->get() as $key) {
            if (!empty($key->charge)) {
                $charges[$key->id] = $key->charge->name;
            }

        }
        $users = [];
        foreach (User::all() as $key) {
            $users[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        return view('check_out.create',
            compact('loan_product', 'borrowers', 'loan_products', 'charges', 'l_custom_fields', 'warehouses',
                'custom_fields', 'suppliers', 'products', 'payment_methods','users'));
    }

    public function get_product_data($product)
    {
        if (!Sentinel::hasAccess('stock.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }

        return View::make('check_in.get_product_data', compact('product'))->render();
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
        $product_check_out = new ProductCheckout();
        $product_check_out->user_id = Sentinel::getUser()->id;
        $product_check_out->type = $request->type;
        $product_check_out->borrower_id = $request->borrower_id;
        $product_check_out->date = $request->date;
        $date = explode('-', $request->date);
        $product_check_out->month = $date[1];
        $product_check_out->year = $date[0];
        $product_check_out->save();
        //save product items
        if (is_array($request->product_id)) {
            for ($i = 0; $i < count($request->product_id); $i++) {
                $product = Product::find($request->product_id[$i]);
                $oq = $product->qty;
                $item = new ProductCheckoutItem();
                $item->product_check_out_id = $product_check_out->id;
                $item->product_id = $request->product_id[$i];
                $item->unit_cost = $product->cost_price;
                $item->total_cost = $request->qty[$i] * $product->cost_price;
                $item->name = $product->name;
                $item->save();
                //increase stock
                $product->qty = $product->qty - $request->qty[$i];
                $product->save();
                if ($oq > $product->alert_qty && $product->qty < $product->alert_qty) {
                    //low product alert
                    $body = $product->name . " is now low in stock. Available quantity:" . $product->qty;
                    Mail::raw($body, function ($message) {
                        $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                            Setting::where('setting_key', 'company_name')->first()->setting_value);
                        $message->to(Setting::where('setting_key', 'company_email')->first()->setting_value);
                        $headers = $message->getHeaders();
                        $message->setContentType('text/html');
                        $message->setSubject("Low Product Alert");

                    });
                }
            }
        }
        //check for payments
        if (!empty($request->paid)) {
            $payment = new ProductPayment();
            $payment->user_id = Sentinel::getUser()->id;
            $payment->product_check_out_id = $product_check_out->id;
            $payment->payment_method_id = $request->payment_method_id;
            $payment->type = "debit";
            $payment->amount = $request->paid;
            $payment->date = $request->date;
            $date = explode('-', $request->date);
            $payment->month = $date[1];
            $payment->year = $date[0];
            $payment->save();
        }

        //try to save file with product name
        /* $custom_fields = CustomField::where('category', 'products_check_out')->get();
         foreach ($custom_fields as $key) {
             $custom_field = new CustomFieldMeta();
             $id = $key->id;
             if ($key->field_type == "checkbox") {
                 if (!empty($request->$id)) {
                     $custom_field->name = serialize($request->$id);
                 } else {
                     $custom_field->name = serialize([]);
                 }
             } else {
                 $custom_field->name = $request->$id;
             }
             $custom_field->parent_id = $product_check_out->id;
             $custom_field->custom_field_id = $key->id;
             $custom_field->category = "products_check_out";
             $custom_field->save();
         }*/
        //check if loan
        if ($request->type == 'loan') {
            $loan = new Loan();
            $loan->principal = $request->principal;
            $loan->product_check_out_id = $product_check_out->id;
            $loan->interest_method = $request->interest_method;
            $loan->interest_rate = $request->interest_rate;
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->branch_id = session('branch_id');
            $loan->interest_period = $request->interest_period;
            $loan->loan_duration = $request->loan_duration;
            $loan->loan_duration_type = $request->loan_duration_type;
            $loan->repayment_cycle = $request->repayment_cycle;
            $loan->decimal_places = $request->decimal_places;
            $loan->override_interest = $request->override_interest;
            $loan->override_interest_amount = $request->override_interest_amount;
            $loan->grace_on_interest_charged = $request->grace_on_interest_charged;
            $loan->borrower_id = $request->borrower_id;
            $loan->applied_amount = $request->principal;
            $loan->user_id = Sentinel::getUser()->id;
            $loan->loan_product_id = $request->loan_product_id;
            $loan->release_date = $request->release_date;
            $date = explode('-', $request->release_date);
            $loan->month = $date[1];
            $loan->year = $date[0];
            if (!empty($request->first_payment_date)) {
                $loan->first_payment_date = $request->first_payment_date;
            }
            $loan->description = $request->description;
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
                        $fname = "loan_" . uniqid() . '.' . $key->guessExtension();
                        $files[$count] = $fname;
                        $key->move(public_path() . '/uploads',
                            $fname);
                    }
                    $count++;
                }
            }
            $loan->files = serialize($files);
            $loan->save();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $amount = "charge_amount_" . $key;
                    $date = "charge_date_" . $key;
                    $loan_charge = new LoanCharge();
                    $loan_charge->loan_id = $loan->id;
                    $loan_charge->user_id = Sentinel::getUser()->id;
                    $loan_charge->charge_id = $key;
                    $loan_charge->amount = $request->$amount;
                    if (!empty($request->$date)) {
                        $loan_charge->date = $request->$date;
                    }
                    $loan_charge->save();
                }
            }

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
            $p = ProductCheckout::find($product_check_out->id);
            $p->loan_id = $loan->id;
            $p->save();
            GeneralHelper::audit_trail("Added check out with id:" . $product_check_out->id);

            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
        GeneralHelper::audit_trail("Added check out with id:" . $product_check_out->id);

        Flash::success(trans('general.successfully_saved'));
        return redirect('check_in/data');
    }


    public function show($product_check_out)
    {
        if (!Sentinel::hasAccess('stock.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }

        //get custom fields
        $custom_fields = CustomField::where('category', 'products_check_in')->get();
        return view('check_in.show', compact('product_check_in', 'custom_fields'));
    }


    public function edit($product_check_out)
    {
        if (!Sentinel::hasAccess('stock.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $warehouses = array();
        foreach (Warehouse::all() as $key) {
            $warehouses[$key->id] = $key->name;
        }
        $suppliers = array();
        foreach (Supplier::all() as $key) {
            $suppliers[$key->id] = $key->name;
        }
        $products = array();
        foreach (Product::all() as $key) {
            $products[$key->id] = $key->name;
        }
        $payment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $payment_methods[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'products_check_in')->get();
        return view('check_in.edit',
            compact('product_check_in', 'custom_fields', 'warehouses', 'suppliers', 'products', 'payment_methods'));
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
        $product_check_out = ProductCheckin::find($id);
        $product_check_out->user_id = Sentinel::getUser()->id;
        $product_check_out->name = $request->name;
        $product_check_out->code = $request->code;
        $product_check_out->cost_price = $request->cost_price;
        $product_check_out->selling_price = $request->selling_price;
        $product_check_out->qty = $request->qty;
        $product_check_out->alert_qty = $request->alert_qty;
        $product_check_out->slug = GeneralHelper::getUniqueSlug($product_check_out, $request->name);
        $product_check_out->notes = $request->notes;
        if ($request->hasFile('picture')) {
            $fname = 'picture-' . $product_check_out->slug . '.' . $request->file('picture')->guessExtension();
            $file = array('picture' => Input::file('picture'));
            $rules = array('picture' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $product_check_out->picture = $fname;
                $request->file('picture')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $product_check_out->save();
        $custom_fields = CustomField::where('category', 'products')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'products')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'products')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            if ($key->field_type == "checkbox") {
                if (!empty($request->$kid)) {
                    $custom_field->name = serialize($request->$kid);
                } else {
                    $custom_field->name = serialize([]);
                }
            } else {
                $custom_field->name = $request->$kid;
            }
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "product";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Updated product with id:" . $product_check_out->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('check_in/data');
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
        ProductCheckin::destroy($id);
        GeneralHelper::audit_trail("Deleted product with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('check_in/data');
    }

    public function overview(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (isset($request->end_date)) {
            $date = $request->end_date;
        } else {
            $date = date("Y-m-d");
        }
        $monthly_stats = array();
        $start_date1 = date_format(date_sub(date_create($date),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $start_date1);
            //get loans in that period
            $check_in = 0;
            $check_out = 0;
            foreach (ProductCheckin::where('branch_id', session('branch_id'))->where('year', $d[0])->where('month',
                $d[1])->get() as $key) {
                $check_in = $check_in + ProductCheckinItem::where('product_check_in_id', $key->id)->sum('qty');
            }
            foreach (ProductCheckout::where('branch_id', session('branch_id'))->where('year', $d[0])->where('month',
                $d[1])->get() as $key) {
                $check_out = $check_out + ProductCheckoutItem::where('product_check_out_id', $key->id)->sum('qty');
            }

            $ext = ' ' . $d[0];
            array_push($monthly_stats, array(
                'month' => date_format(date_create($start_date1),
                    'M' . $ext),
                'check_in' => $check_in,
                'check_out' => $check_out

            ));
            //add 1 month to start date
            $start_date1 = date_format(date_add(date_create($start_date1),
                date_interval_create_from_date_string('1 months')),
                'Y-m-d');
        }
        $monthly_stats = json_encode($monthly_stats);
        return view('check_out.overview',
            compact('start_date', 'end_date', 'monthly_stats'));
    }
}
