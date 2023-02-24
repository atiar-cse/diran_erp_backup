<?php

namespace App\Http\Controllers\HR\Employee;

use App\Model\HR\HrDepartment;
use App\Model\HR\HrDesignation;
use App\Model\HR\HrManageEmployee;
use App\Model\HR\HrIncreament;
use App\Model\Payroll\PayrollMonthlyPayGrade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class IncreamentController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrIncreament::with('get_employee','get_department','get_designation','get_current_paygrade','get_promoted_paygrade','get_promoted_department','get_promoted_designation')->where('status','=',1)->orderBy('id','desc')->paginate($request->perPage);
        }

        $employeeLists         = HrManageEmployee::with('emp_department','emp_designation','get_pay_grade','get_salary')->where('status','1')->where('permanent_status','=',1)->get();
        $departmentLists       = HrDepartment::where('status', '1')->get();
        $designationLists      = HrDesignation::where('status', '1')->get();
        $payGradeLists         = PayrollMonthlyPayGrade::where('status', '1')->get();

        return view('hr.employee_section.increament',compact('employeeLists','departmentLists','designationLists','payGradeLists'));
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id'                =>'required',
            'current_department_display' =>'required',
            'current_designation_display'=>'required',
            'new_salary'                 =>'required',
        ]);

        $input = $request->all();

        $input['increment_date'] = dateConvertFormtoDB($request->increment_date);
        $input['created_by']     = Auth::user()->id;


        try {
            DB::beginTransaction();

            HrIncreament::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Increament Entry successfully saved!']);
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
            $editModeData = HrIncreament::FindOrFail($id);

            $data = [
                'id'                    => $editModeData->id,
                'employee_id'           => $editModeData->employee_id,
                'current_department_id' => $editModeData->current_department_id,
                'current_designation_id'=> $editModeData->current_designation_id,
                'current_pay_grade_id'  => $editModeData->current_pay_grade_id,
                'current_salary'        => $editModeData->current_salary,
                'new_salary'            => $editModeData->new_salary,
                'increment_date'        => dateConvertDBtoForm($editModeData->increment_date),
                'narration'             => $editModeData->narration,
                'current_department_display' => '', //Populated from frontend
                'current_designation_display' => '', //Populated from frontend
                'current_pay_grade_display' => '', //Populated from frontend
                'current_salary_display' => '', //Populated from frontend
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_id'                =>'required',
            'current_department_display' =>'required',
            'current_designation_display'=>'required',
            'new_salary'                 =>'required',
        ]);

        $input = $request->all();
        $input['increment_date'] = dateConvertFormtoDB($request->increment_date);
        $input['updated_by']     = Auth::user()->id;

        try {
            DB::beginTransaction();

            $editModeData = HrIncreament::FindOrFail($id);
            $editModeData->update($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Increament Entry successfully updated!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        try{
            HrIncreament::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Increament Entry successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
