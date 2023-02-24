<?php

namespace App\Http\Controllers\Production\Setup;

use App\Model\Country;
use App\Model\Production\ProductionBrand;
use App\Model\Production\ProductionCategory;
use App\Model\Production\ProductionMeasureUnit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Model\Production\ProductionProducts;

class ProductEntryController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('production_products')
                ->leftJoin('production_categories', 'production_products.category_id', '=', 'production_categories.id')
                ->leftJoin('production_brands', 'production_products.brand_id', '=', 'production_brands.id')
                ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->leftJoin('users as ura', 'production_products.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_products.updated_by','=','ured.id')
                ->whereNull('production_products.deleted_at')
                ->select(['production_products.id AS id',
                        'production_products.product_code',
                        'production_products.product_name',
                        'production_products.buying_price',
                        'production_products.selling_price',
                        'production_products.created_by',
                        'production_products.updated_by',
                        'production_products.status',
                        'production_products.is_raw_material_status',
                        'production_categories.product_category_name',
                        'production_brands.product_brand_name',
                        'production_measure_units.measure_unit',
                        'ura.user_name as ad_name',
                        'ured.user_name as ed_name'
                    ]);

            return datatables()->of($query)->toJson();
        }

        $brandList      = ProductionBrand::where('status', '1')->get();
        $categoryList   = ProductionCategory::where('status', '1')->get();
        $measureUnitList= ProductionMeasureUnit::where('status', '1')->get();
        $countryList    = Country::get();

        $data = [
            'brandList'        => $brandList,
            'categoryList'   => $categoryList,
            'measureUnitList'   => $measureUnitList,
            'countryList'=> $countryList
        ];

        return view('production.production_setup.products_entry',$data);
    }





    public function store(Request $request)
    {
        $this->validate($request, [
            'product_name'=>'required|unique:production_products,product_name',
            'category_id'=>'required',
            'measure_unit_id'=>'required',
            'credit_limit'=>'nullable|numeric',
            'buying_price'=>'nullable|numeric',
            'selling_price'=>'nullable|numeric',
            'complete_product_weight'=>'nullable|numeric',
            'forming_weight'=>'nullable|numeric',
            'shapping_weight'=>'nullable|numeric',
            'dryer_weight'=>'nullable|numeric',
            'glaze_weight'=>'nullable|numeric',
            'kiln_weight'=>'nullable|numeric',
        ]);

        $input = $request->all();
        if ($request->is_raw_material_status == null){
            $input['is_raw_material_status'] = 0;
        }
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            ProductionProducts::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Product successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Product is Found Duplicate !']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        return ProductionProducts::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'product_name'=>'required',
            'category_id'=>'required',
            'measure_unit_id'=>'required',
            'credit_limit'=>'nullable|numeric',
            'buying_price'=>'nullable|numeric',
            'selling_price'=>'nullable|numeric',
            'complete_product_weight'=>'nullable|numeric',
            'forming_weight'=>'nullable|numeric',
            'shapping_weight'=>'nullable|numeric',
            'dryer_weight'=>'nullable|numeric',
            'glaze_weight'=>'nullable|numeric',
            'kiln_weight'=>'nullable|numeric',
        ]);

        $data = ProductionProducts::findOrFail($id);
        $input = $request->all();
        if ($request->is_raw_material_status == null){
            $input['is_raw_material_status'] = 0;
        }
        $input['updated_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            $data->update($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Product successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Product is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = ProductionProducts ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Product entry successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
