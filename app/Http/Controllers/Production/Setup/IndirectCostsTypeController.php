<?php

namespace App\Http\Controllers\Production\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Model\Production\ProductionIndirectCostsType;

class IndirectCostsTypeController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(ProductionIndirectCostsType::query())->toJson();
        }
        
        return view('production.production_setup.indirect_costs_type');
    }


    public function create()
    {
        
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'indirect_cost_type'=>'required',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            ProductionIndirectCostsType::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Costs Type successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Costs Type is Found Duplicate !']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {

    }


    public function edit($id)
    {
        return ProductionIndirectCostsType::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'indirect_cost_type'=>'required',
            'status'=>'required',
        ]);

        $data = ProductionIndirectCostsType::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Costs Type successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Costs Type is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = ProductionIndirectCostsType::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Costs Type successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
