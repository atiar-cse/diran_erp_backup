<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Payroll\PayrollBonusSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class BonusSettingController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return PayrollBonusSetting::orderBy('id','desc')->paginate($request->perPage);
        }
        return view('payroll.payroll_setup.bonus_setting');
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'festival_name'      =>'required',
            'percentage_of_bonus'=>'required',
            'bonus_type'         =>'required',
        ]);

        $data =[
            'festival_name'      =>$request->festival_name,
            'percentage_of_bonus'=>$request->percentage_of_bonus,
            'bonus_type'         =>$request->bonus_type,
            'created_by'         =>Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            PayrollBonusSetting::create($data);

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
        return PayrollBonusSetting::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'festival_name'      =>'required',
            'percentage_of_bonus'=>'required',
            'bonus_type'         =>'required',
        ]);

        $data =[
            'festival_name'      =>$request->festival_name,
            'percentage_of_bonus'=>$request->percentage_of_bonus,
            'bonus_type'         =>$request->bonus_type,
            'updated_by'         =>Auth::user()->id,
        ];

        try {
            DB::beginTransaction();
            $editModeData = PayrollBonusSetting::FindOrFail($id);
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
        $data = PayrollBonusSetting::FindOrFail($id);
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
