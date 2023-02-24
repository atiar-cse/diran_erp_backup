<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Payroll\PayGradeToAllowance;
use App\Model\Payroll\PayGradeToDeduction;
use App\Model\Payroll\PayrollAllowance;
use App\Model\Payroll\PayrollDeduction;
use App\Model\Payroll\PayrollMonthlyPayGrade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

class MonthlyPayGradeController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return PayrollMonthlyPayGrade::orderBy('id','desc')->paginate($request->perPage);
        }
        $allowanceList =PayrollAllowance::where('status','1')->get();
        $deductionList =PayrollDeduction::where('status','1')->get();
        return view('payroll.payroll_setup.monthly_pay_grade',compact('allowanceList','deductionList'));
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'pay_grade_name'=>'required|unique:payroll_monthly_pay_grades,pay_grade_name',
            'gross_salary'=>'required',
            'percentage_of_basic'=>'required',
            'basic_salary'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;


        try {
            DB::beginTransaction();

            $result = PayrollMonthlyPayGrade::create($input);

            $allowanceData = $this->allowanceDataFormat($request, $result->id);
            PayGradeToAllowance::insert($allowanceData);

            $deductionData = $this->deductionDataFormat($request, $result->id);
            PayGradeToDeduction::insert($deductionData);



            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Monthly Pay Grade data successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Monthly Pay Grade data is Found Duplicate']);
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

        try {
            $editModeData        = PayrollMonthlyPayGrade::FindOrFail($id);
            $editModeDetailsData = PayGradeToAllowance::where('pay_grade_id',$id)->get();
            //$editModeDetailsData = PayGradeToDeduction::where('pay_grade_id',$id)->get();
            $data = [
                'id'                  => $editModeData->id,
                'pay_grade_name'      => $editModeData->pay_grade_name,
                'gross_salary'        => $editModeData->gross_salary,
                'percentage_of_basic' => $editModeData->percentage_of_basic,
                'basic_salary'        => $editModeData->basic_salary,
                'over_time_rate'      => $editModeData->over_time_rate,
                'selectedAllowance'   => $editModeDetailsData,

            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function update(Request $request, $id)
    {
        $data = [
            'pay_grade_name'      => $request->pay_grade_name,
            'gross_salary'        => $request->gross_salary,
            'percentage_of_basic' => $request->percentage_of_basic,
            'basic_salary'        => $request->basic_salary,
            'over_time_rate'      => $request->over_time_rate,
            'updated_by'          => Auth::user()->id,
        ];
        $editModeData = PayrollMonthlyPayGrade::FindOrFail($id);
        $editModeData->update($data);

       /* $allowance = [];
        if (isset($request->selectedAllowance)) {
            $count = count($request->selectedAllowance);
            for ($i = 0; $i < $count; $i++) {
                $allowance[$i] = [
                    'pay_grade_id' => $id,
                    'allowance_id' => $request->selectedAllowance[$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }


             PayGradeToAllowance::insert($allowance);*/


        try {
            DB::beginTransaction();



            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Monthly Pay Grade data successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Monthly Pay Grade data is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }



    }


    public function destroy($id)
    {
        try{

            PayGradeToAllowance::where('pay_grade_id',$id)->delete();
            PayGradeToDeduction::where('pay_grade_id',$id)->delete();
            PayrollMonthlyPayGrade::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Monthly Pay Grade successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function allowanceDataFormat($request, $id)
    {
        $allowance = [];
        if (isset($request->selectedAllowance)) {
            $count = count($request->selectedAllowance);
            for ($i = 0; $i < $count; $i++) {
                $allowance[$i] = [
                    'pay_grade_id' => $id,
                    'allowance_id' => $request->selectedAllowance[$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        return $allowance;
    }


    public function deductionDataFormat($request, $id)
    {
        $deduction =[];
        if (isset($request->selectedDeduction)) {
            $count = count($request->selectedDeduction);
            for ($i = 0; $i < $count; $i++) {
                $deduction[$i] = [
                    'pay_grade_id' => $id,
                    'deduction_id' => $request->selectedDeduction[$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

        }

        return $deduction;
    }
}
