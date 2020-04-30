<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;

use App\Models\Branch;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\LoanRepaymentMethod;
use App\Models\Product;
use App\Models\ProductCheckin;
use App\Models\ProductCheckinItem;
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

class ProductCheckinController extends Controller
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
        $data = ProductCheckin::all();

        return view('check_in.data', compact('data'));
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
        return view('check_in.create', compact('warehouses', 'custom_fields', 'suppliers', 'products','payment_methods'));
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
        $product_check_in = new ProductCheckin();
        $product_check_in->user_id = Sentinel::getUser()->id;
        $product_check_in->supplier_id = $request->supplier_id;
        $product_check_in->warehouse_id = $request->warehouse_id;
        $product_check_in->date = $request->date;
        $date = explode('-', $request->date);
        $product_check_in->month = $date[1];
        $product_check_in->year = $date[0];
        $product_check_in->save();
        //save product items
        if (is_array($request->product_id)) {
            for($i=0;$i<count($request->product_id);$i++) {
                $product=Product::find($request->product_id[$i]);
                $item=new ProductCheckinItem();
                $item->product_check_in_id=$product_check_in->id;
                $item->product_id=$request->product_id[$i];
                $item->unit_cost=$product->cost_price;
                $item->total_cost=$request->qty[$i]*$product->cost_price;
                $item->name=$product->name;
                $item->save();
                //increase stock
                $product->qty=$product->qty+$request->qty[$i];
                $product->save();
            }
        }
        //check for payments
        if(!empty($request->paid)){
            $payment=new ProductPayment();
            $payment->user_id = Sentinel::getUser()->id;
            $payment->product_check_in_id = $product_check_in->id;
            $payment->payment_method_id = $request->payment_method_id;
            $payment->type="debit";
            $payment->amount=$request->paid;
            $payment->date = $request->date;
            $date = explode('-', $request->date);
            $payment->month = $date[1];
            $payment->year = $date[0];
            $payment->save();
        }

        //try to save file with product name
        $custom_fields = CustomField::where('category', 'products_check_in')->get();
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
            $custom_field->parent_id = $product_check_in->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "products_check_in";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Added check in with id:" . $product_check_in->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('check_in/data');
    }


    public function show($product_check_in)
    {
        if (!Sentinel::hasAccess('stock.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }

        //get custom fields
        $custom_fields = CustomField::where('category', 'products_check_in')->get();
        return view('check_in.show', compact('product_check_in', 'custom_fields'));
    }


    public function edit($product_check_in)
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
        return view('check_in.edit', compact('product_check_in', 'custom_fields', 'warehouses', 'suppliers','products','payment_methods'));
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
        $product_check_in = ProductCheckin::find($id);
        $product_check_in->user_id = Sentinel::getUser()->id;
        $product_check_in->name = $request->name;
        $product_check_in->code = $request->code;
        $product_check_in->cost_price = $request->cost_price;
        $product_check_in->selling_price = $request->selling_price;
        $product_check_in->qty = $request->qty;
        $product_check_in->alert_qty = $request->alert_qty;
        $product_check_in->slug = GeneralHelper::getUniqueSlug($product_check_in, $request->name);
        $product_check_in->notes = $request->notes;
        if ($request->hasFile('picture')) {
            $fname = 'picture-' . $product_check_in->slug . '.' . $request->file('picture')->guessExtension();
            $file = array('picture' => Input::file('picture'));
            $rules = array('picture' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $product_check_in->picture = $fname;
                $request->file('picture')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $product_check_in->save();
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
        GeneralHelper::audit_trail("Updated product with id:" . $product_check_in->id);
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


}
