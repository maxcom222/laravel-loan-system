<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;
use App\Models\ProductCategory;
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

class ProductCategoryController extends Controller
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
        $data = ProductCategory::all();
        $menus = array(
            'items' => array(),
            'parents' => array()
        );
        // Builds the array lists with data from the SQL result
        foreach (ProductCategory::all() as $items) {
            // Create current menus item id into array
            $menus['items'][$items['id']] = $items;
            // Creates list of all items with children
            $menus['parents'][$items['parent_id']][] = $items['id'];
        }
        $tree = GeneralHelper::buildTree(ProductCategory::all());
        return view('product_category.data', compact('data', 'menus','tree'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $product_categories = array();
        $product_categories["0"] = trans('general.none');
        foreach (ProductCategory::all() as $key) {
            $product_categories[$key->id] = $key->name;
        }
        $tree = GeneralHelper::buildTree(ProductCategory::all());
        return view('product_category.create', compact('product_categories','tree'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $product_category = new ProductCategory();
        $product_category->name = $request->name;
        $product_category->parent_id = $request->parent_id;
        $product_category->user_id = $request->user_id;
        $product_category->notes = $request->notes;
        $product_category->slug = GeneralHelper::getUniqueSlug($product_category,$request->name);
        $product_category->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('product/category/data');
    }


    public function show($product_category)
    {
        $product_categories = array();

        return view('product_category.show', compact('product_category', 'members'));
    }


    public function edit($product_category)
    {
        $product_categories = array();
        $product_categories["0"] = trans('general.none');
        foreach (ProductCategory::all() as $key) {
            $product_categories[$key->id] = $key->name;
        }
        $product_categories = array_except($product_categories, $product_category->id);
        $tree = GeneralHelper::buildTree(ProductCategory::where('id','!=',$product_category->id)->get());
        return view('product_category.edit', compact('product_category', 'product_categories','tree'));
    }

    public function category_data($product_category)
    {
        $json = array();
        $json["name"] = $product_category->name;
        $json["notes"] = $product_category->notes;
        echo json_encode($json, JSON_UNESCAPED_SLASHES);
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
        $product_category = ProductCategory::find($id);
        $product_category->name = $request->name;
        $product_category->slug = str_slug($request->name, '-');
        $product_category->notes = $request->notes;
        $product_category->active = $request->active;
        $product_category->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('product/category/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        ProductCategory::destroy($id);
        ProductCategory::where('parent_id', $id)->delete();
        Flash::success(trans('general.successfully_deleted'));
        return redirect('product/category/data');
    }

}
