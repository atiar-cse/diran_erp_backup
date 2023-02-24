<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Payroll\PayrollSalaryProcess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\HR\HrManageEmployee;

class GenerateSalarySheetController extends Controller
{

    public function index()
    {
        $employeeLists   = HrManageEmployee::where('status','1')->get();
        return view('payroll.payroll_section.generate_salary_sheet',compact('employeeLists'));
    }


    public function generateSalarySheet(Request $request)
    {

        $return = '';

        if ($request->ajax()) {

            $employee_id   = $request->employee_id;

            $salary_month  = date('m',strtotime($request->month));


            $return = PayrollSalaryProcess::with('get_emp_name')->whereMonth('salary_month',$salary_month)->get();

           /* if($employee_id!='' && $salary_date!='' ){
//                $return = HrManageEmployee::where('status',1)->get();
                $return =PayrollSalaryProcess::select('payroll_salary_processes.*','hr_manage_employees.first_name','hr_manage_employees.last_name','hr_manage_employees.fingerprint_no','hr_departments.department_name','hr_designations.designation_name')
                    ->leftJoin('hr_manage_employees','hr_manage_employees.id','=','payroll_salary_processes.employee_id')
                    ->leftJoin('hr_departments','hr_departments.id','=','hr_manage_employees.department_id')
                    ->leftJoin('hr_designations','hr_designations.id','=','hr_manage_employees.designation_id')
                    ->where('employee_id',$employee_id)->where('salary_month',$salary_date)->first();
                //dd($return);
                }*/

                return $return;


        }

    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
