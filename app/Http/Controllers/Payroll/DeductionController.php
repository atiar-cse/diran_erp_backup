<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Payroll\PayrollDeduction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class DeductionController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return PayrollDeduction::orderBy('id','desc')->paginate($request->perPage);
        }
        return view('payroll.payroll_setup.deduction');

    }


     
    public function store(Request $request)
    {
        $this->validate($request, [
            'deduction_name'     =>'required',
            'deduction_type'     =>'required',
            'percentage_of_basic'=>'required',
            'limit_per_month'    =>'required',
        ]);

        $data =[
            'deduction_name'     =>$request->deduction_name,
            'deduction_type'     =>$request->deduction_type,
            'percentage_of_basic'=>$request->percentage_of_basic,
            'limit_per_month'    =>$request->limit_per_month,
            'created_by'         =>Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            PayrollDeduction::create($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Deduction successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Deduction is Found Duplicate']);
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
        return PayrollDeduction::FindOrFail($id);
    }

     
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'deduction_name'     =>'required',
            'deduction_type'     =>'required',
            'percentage_of_basic'=>'required',
            'limit_per_month'    =>'required',
        ]);

        $data =[
            'deduction_name'     =>$request->deduction_name,
            'deduction_type'     =>$request->deduction_type,
            'percentage_of_basic'=>$request->percentage_of_basic,
            'limit_per_month'    =>$request->limit_per_month,
            'updated_by'         =>Auth::user()->id,
        ];

        try {
            DB::beginTransaction();
            $editModeData = PayrollDeduction::FindOrFail($id);
            $editModeData->update($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Deduction successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Deduction is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

     
    public function destroy($id)
    {
        $data = PayrollDeduction::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Deduction successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
