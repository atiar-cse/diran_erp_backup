<?php

namespace App\Http\Controllers\Production\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Production\ProductionCategory;
use Illuminate\Support\Facades\Auth;
use DB;

class ProductCategoryController extends Controller
{

    public function index(Request $request)
    {
        return datatables()->of(ProductionCategory::query())->toJson();

    }


    public function create()
    {
         return view('production.production_setup.products_category');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'product_category_name'=>'required|unique:production_categories,product_category_name',
            'product_category_code'=>'required|unique:production_categories,product_category_code',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            ProductionCategory::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Product Category successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Product Category is Found Duplicate']);
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
        return ProductionCategory::FindOrFail($id);
       /*try {
            $data = ProductionCategory::findOrFail($id);
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }*/
    }


    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'product_category_name'=>'required',
            'product_category_code'=>'required',
            'status'=>'required',
        ]);

        $data = ProductionCategory::findOrFail($id);
        $input = $request->all();
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
            return response()->json(['status' => 'success', 'message' => 'Product Category successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Product Category is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = ProductionCategory::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Product Category successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }

    }
}
