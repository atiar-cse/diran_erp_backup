<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Payroll\PayrollHourlyPayGrade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

class HourlyPayGradeController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return PayrollHourlyPayGrade::orderBy('id','desc')->paginate($request->perPage);
        }
        return view('payroll.payroll_setup.hourly_pay_grade');
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'hourly_pay_grade_name'=>'required|unique:payroll_hourly_pay_grades,hourly_pay_grade_name',
            'hourly_rate'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;


        try {
            DB::beginTransaction();

            PayrollHourlyPayGrade::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Hourly Pay Grade data successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Hourly Pay Grade data is Found Duplicate']);
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
        return PayrollHourlyPayGrade::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'hourly_pay_grade_name'=>'required',
            'hourly_rate'=>'required',
        ]);

        $data = PayrollHourlyPayGrade::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Hourly Pay Grade data successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Hourly Pay Grade data is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = PayrollHourlyPayGrade::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Hourly Pay Grade data successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
