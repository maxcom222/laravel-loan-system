<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;

use App\Models\Branch;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryMeta;
use App\Models\ProductReview;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class ProductController extends Controller
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
        if (!Sentinel::hasAccess('stock')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $data = Product::all();

        return view('product.data', compact('data'));
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
        $categories = array();
        foreach (ProductCategory::all() as $key) {
            $categories[$key->id] = $key->name;
        }

        $tree = GeneralHelper::buildTree(ProductCategory::all());
        //get custom fields
        $custom_fields = CustomField::where('category', 'products')->get();
        return view('product.create', compact('categories', 'custom_fields', 'branches', 'tree'));
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
        $product = new Product();
        $product->user_id = Sentinel::getUser()->id;
        $product->name = $request->name;
        $product->code = $request->code;
        $product->cost_price = $request->cost_price;
        $product->selling_price = $request->selling_price;
        $product->qty = $request->qty;
        $product->alert_qty = $request->alert_qty;
        $product->slug = GeneralHelper::getUniqueSlug($product, $request->name);
        $product->notes = $request->notes;
        if ($request->hasFile('picture')) {
            $fname = 'picture-' . $product->slug . '.' . $request->file('picture')->guessExtension();
            $file = array('picture' => Input::file('picture'));
            $rules = array('picture' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $product->picture = $fname;
                $request->file('picture')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $product->save();
        //save product categories
        if (is_array($request->categories)) {
            foreach ($request->categories as $cat) {
                $category = new ProductCategoryMeta();
                $category->product_id = $product->id;
                $category->product_category_id = $cat;
                $category->save();
            }
        }
        //try to save file with product name
        $custom_fields = CustomField::where('category', 'products')->get();
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
            $custom_field->parent_id = $product->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "product";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Added product with id:" . $product->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('product/data');
    }


    public function show($product)
    {
        if (!Sentinel::hasAccess('stock.view')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }

        //get custom fields
        $custom_fields = CustomField::where('category', 'products')->get();
        return view('product.show', compact('product', 'custom_fields'));
    }


    public function edit($product)
    {
        if (!Sentinel::hasAccess('stock.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $categories = array();
        foreach (ProductCategory::all() as $key) {
            $categories[$key->id] = $key->name;
        }
        $tree = GeneralHelper::buildTree(ProductCategory::all());
        //get custom fields
        $custom_fields = CustomField::where('category', 'products')->get();
        return view('product.edit', compact('product', 'custom_fields', 'categories', 'tree'));
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
        $product = Product::find($id);
        $product->user_id = Sentinel::getUser()->id;
        $product->name = $request->name;
        $product->code = $request->code;
        $product->cost_price = $request->cost_price;
        $product->selling_price = $request->selling_price;
        $product->qty = $request->qty;
        $product->alert_qty = $request->alert_qty;
        $product->slug = GeneralHelper::getUniqueSlug($product, $request->name);
        $product->notes = $request->notes;
        if ($request->hasFile('picture')) {
            $fname = 'picture-' . $product->slug . '.' . $request->file('picture')->guessExtension();
            $file = array('picture' => Input::file('picture'));
            $rules = array('picture' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $product->picture = $fname;
                $request->file('picture')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $product->save();
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
        GeneralHelper::audit_trail("Updated product with id:" . $product->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('product/data');
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
        Product::destroy($id);
        GeneralHelper::audit_trail("Deleted product with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('product/data');
    }


}
