<?php

namespace App\Http\Controllers\Accounts\BasicSetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsCostCenter;

use Illuminate\Support\Facades\Auth;
use DB;

class CostCenterController extends Controller
{

    public function index(Request $request)
    {
        return AccountsCostCenter::orderBy('id','desc')->paginate($request->perPage);
    }

    public function create()
    {
        return view('accounts.accounts_setup.cost_centre');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'cost_center_name'=>'required|unique:accounts_cost_centers,cost_center_name',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();
                AccountsCostCenter::create($input);
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Cost Centre successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Cost Centre is Found Duplicate']);
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
        return AccountsCostCenter::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'cost_center_name'=>'required|unique:accounts_cost_centers,cost_center_name,'.$id.',id',
            'status'=>'required',
        ]);

        $data = AccountsCostCenter::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Cost Centre successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Cost Centre is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = AccountsCostCenter ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Cost Centre successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
