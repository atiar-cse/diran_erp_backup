<?php

namespace App\Http\Controllers\HR\Attendance;

use App\Model\HR\HrDepartment;
use App\Model\HR\HrManageEmployee;
use App\Model\HR\HrManageWorkShift;
use App\Model\HR\HrManualAttendanceEntry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;

class ManualAttendanceEntryController extends Controller
{

    public function index()
    {
        $departmentLists  = HrDepartment::where('status', '1')->orderBy('id','ASC')->get();
        $workShiftLists   = HrManageWorkShift::where('status', '1')->orderBy('id','ASC')->get();
        $employeeLists    = HrManageEmployee::where('status', '1')->orderBy('id','ASC')->get();
        return view('hr.attendance_section.manual_attendance_entry',compact( 'departmentLists','employeeLists','workShiftLists'));
    }


    public function employeeAttendanceLoad($department_id,$shift_id,$date)
    {
        $employeeLists = HrManageEmployee::select(DB::raw('hr_manage_employees.id,hr_manage_employees.first_name,hr_manage_employees.last_name,hr_manage_employees.department_id,hr_manage_employees.work_shift_id,hr_manual_attendance_entries.start_time,hr_manual_attendance_entries.end_time'))
            ->leftJoin('hr_manual_attendance_entries', 'hr_manual_attendance_entries.employee_attendance_employee_id', '=', 'hr_manage_employees.id')
            ->where('hr_manual_attendance_entries.employee_attendance_department_id',$department_id)
            ->where('hr_manual_attendance_entries.employee_shift_id',$shift_id)
            ->where('hr_manual_attendance_entries.emp_attendance_date',$date)
            ->get()->toArray();

            if(!empty($employeeLists)){

                return response()->json($employeeLists);
            }
            else{
                $employeeData = HrManageEmployee::select('id','first_name','last_name','department_id')->where("department_id",$department_id)->get();

                $employeeDataFormat = [];
                $count = count($employeeData);
                for ($i = 0; $i < $count; $i++) {
                    $employeeDataFormat[$i] = [
                        'id'            => $employeeData[$i]['id'],
                        'first_name'    => $employeeData[$i]['first_name'],
                        'last_name'     => $employeeData[$i]['last_name'],
                        'department_id' => $employeeData[$i]['department_id'],
                        'work_shift_id' => '',
                        'start_time'    => '',
                        'end_time'      => '',
                    ];
                }
                return response()->json($employeeDataFormat);

            }

    }





    public function store(Request $request)
    {
        $departmentId = $request->employee_attendance_department_id;
        $shiftId      = $request->employee_shift_id;
        $attdate      = $request->emp_attendance_date;

        $attendanceData = [];

        $count = count($request->employee);

        for ($i = 0; $i < $count; $i++) {

            $start_time   = date("H:i:s", strtotime($request->employee[$i]['start_time']));
            $end_time     = date("H:i:s", strtotime($request->employee[$i]['end_time']));

            if (isset($request->employee[$i]['id']) && $request->employee[$i]['id'] !=0) {
                $attendanceData[$i]=[
                    'employee_attendance_department_id' => $departmentId,
                    'employee_shift_id'                 => $shiftId,
                    'emp_attendance_date'               => $attdate,
                    'employee_attendance_employee_id'   => $request->employee[$i]['id'],
                    'start_time'                        => $start_time,
                    'end_time'                          => $end_time,
                    'created_by'                        => Auth::user()->id
                ];

            }

        }


        try {
            DB::beginTransaction();

            $result = HrManualAttendanceEntry::where('employee_attendance_department_id',$departmentId)->orWhere('employee_shift_id',$shiftId)->orWhere('emp_attendance_date',$attdate)->first();

            if ($result == null){
                HrManualAttendanceEntry::insert($attendanceData);
            }else{
                DB::table('hr_manual_attendance_entries')->where('employee_attendance_department_id',$departmentId)->where('employee_shift_id',$shiftId)->where('emp_attendance_date',$attdate)->delete();
                HrManualAttendanceEntry::insert($attendanceData);
            }


            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Employee Attendance successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }




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
