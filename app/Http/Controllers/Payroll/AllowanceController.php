<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Payroll\PayrollAllowance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class AllowanceController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return PayrollAllowance::orderBy('id','desc')->paginate($request->perPage);
        }
        return view('payroll.payroll_setup.allowance');
    }




    public function store(Request $request)
    {
        $this->validate($request, [
            'allowance_name'     =>'required',
            'allowance_type'     =>'required',
            'percentage_of_basic'=>'required',
            'limit_per_month'    =>'required',
        ]);

        $data =[
            'allowance_name'     =>$request->allowance_name,
            'allowance_type'     =>$request->allowance_type,
            'percentage_of_basic'=>$request->percentage_of_basic,
            'limit_per_month'    =>$request->limit_per_month,
            'created_at'         =>Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            PayrollAllowance::create($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Allowance successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Allowance is Found Duplicate']);
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
        return PayrollAllowance::FindOrFail($id);

    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'allowance_name'     =>'required',
            'allowance_type'     =>'required',
            'percentage_of_basic'=>'required',
            'limit_per_month'    =>'required',
        ]);

        $data =[
            'allowance_name'     =>$request->allowance_name,
            'allowance_type'     =>$request->allowance_type,
            'percentage_of_basic'=>$request->percentage_of_basic,
            'limit_per_month'    =>$request->limit_per_month,
            'updated_at'         =>Auth::user()->id,
        ];

        try {
            DB::beginTransaction();
            $editModeData = PayrollAllowance::FindOrFail($id);
            $editModeData->update($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Allowance successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Allowance is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = PayrollAllowance::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Allowance successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
