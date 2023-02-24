<?php

namespace App\Http\Controllers\LcImport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LC\LcCostNameCategory;
use Illuminate\Support\Facades\Auth;
use DB;

class LcCostNameCategoryEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(LcCostNameCategory::query())->toJson();
        }

        return view('lc.lc_setup.lc_cost_name_category_entry');
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'cost_category_name'=>'required|unique:lc_cost_name_categories,cost_category_name',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            LcCostNameCategory::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Cost Category successfully saved !']);
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
        return LcCostNameCategory::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'cost_category_name'=>'required|unique:lc_cost_name_categories,cost_category_name,'.$id,
            'status'=>'required',
        ]);

        $data = LcCostNameCategory::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'LC Cost Category successfully Updated !']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = LcCostNameCategory::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Cost Category successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }

    }   
}
