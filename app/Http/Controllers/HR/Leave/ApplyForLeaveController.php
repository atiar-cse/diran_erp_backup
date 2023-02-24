<?php

namespace App\Http\Controllers\HR\Leave;

use App\Model\HR\HrEmployeeType;
use App\Model\HR\LeaveTitle;
use App\Model\UserAccessControl\AclMenuPermission;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\HR\HrManageEmployee;
use App\Model\HR\HrApplyForLeave;
use App\Model\HR\HrLeaveType;
use Illuminate\Support\Facades\Auth;
use DB;

class ApplyForLeaveController extends Controller
{

    public function index(Request $request)
    {
        $role_id = Auth::user()->role_id;

        $user_id = Auth::user()->id;

        $emp_id = HrManageEmployee::select('id')->where('user_id',$user_id)->first();

        //please keep this in mind
        //that in the menu seeder table data can not be changed

        //$menu_id = '';

        $menu_permission_check = AclMenuPermission::where('role_id',$role_id)->where('menu_id',25631)->first();

        if ($request->ajax()) {

            if($menu_permission_check){
                $result =DB::table('hr_apply_for_leaves')
                    ->leftJoin('hr_manage_employees','hr_apply_for_leaves.employee_id','=','hr_manage_employees.id')
                    ->leftJoin('leave_titles','hr_apply_for_leaves.leave_type_id','=','leave_titles.id')
                    ->select('hr_apply_for_leaves.id',
                        'hr_apply_for_leaves.leave_type_id',
                        'hr_apply_for_leaves.employee_id',
                        'hr_apply_for_leaves.application_from_date',
                        'hr_apply_for_leaves.application_to_date',
                        'hr_apply_for_leaves.number_of_day',
                        'hr_apply_for_leaves.status',
                        'hr_manage_employees.first_name',
                        'hr_manage_employees.last_name',
                        'leave_titles.leave_title'
                    )
                    ->orderBy('hr_apply_for_leaves.application_from_date','desc')
                    ->paginate($request->perPage);

                //dd($result);
            }else{

                $result =DB::table('hr_apply_for_leaves')
                    ->leftJoin('hr_manage_employees','hr_apply_for_leaves.employee_id','=','hr_manage_employees.id')
                    ->leftJoin('leave_titles','hr_apply_for_leaves.leave_type_id','=','leave_titles.id')
                    ->select('hr_apply_for_leaves.id',
                        'hr_apply_for_leaves.leave_type_id',
                        'hr_apply_for_leaves.application_from_date',
                        'hr_apply_for_leaves.application_to_date',
                        'hr_apply_for_leaves.number_of_day',
                        'hr_apply_for_leaves.status',
                        'hr_manage_employees.first_name',
                        'hr_manage_employees.last_name',
                        'leave_titles.leave_title'
                    )
                    ->where('hr_apply_for_leaves.employee_id',$emp_id->id)
                    ->orderBy('hr_apply_for_leaves.application_from_date','desc')
                    ->paginate($request->perPage);
            }

            return $result;

        }
        if($menu_permission_check){
            $employeeLists    = HrManageEmployee::where('status', '1')->get();
        }else{
            $employeeLists    = HrManageEmployee::where('status', '1')->where('user_id',Auth::user()->id)->get();
        }

        $leaveTypeLists   = LeaveTitle::where('status', '1')->get();
        return view('hr.leave_section.apply_for_leave',compact('employeeLists','leaveTypeLists'));
    }

    public function checkLeaveBalance(Request $request)
    {
        if($request->ajax()) {

            $empId = $request->employee_id;

            $calculateEmpLeaveBalance = [];

            if ($empId != '') {

                 $employeeTypeId = HrManageEmployee::select('employee_type_id')->where('status','=',1)->where('id',$empId)->first();

                 if($employeeTypeId)
                 {
                     $employeeTypeId = $employeeTypeId->employee_type_id;

                     $leaveTypeData = HrLeaveType::select('leave_type_name','number_of_day')->where('employee_type_id',$employeeTypeId)->orderBy('leave_type_name','ASC')->get();

                        if($leaveTypeData->count() > 0)
                        {
                            foreach ($leaveTypeData as $type){

                                $calculateEmpLeaveBalance[] =[
                                    'title'        =>  LeaveTitle::select('leave_title')->where('id',$type->leave_type_name)->first()->leave_title,
                                    'balance'      =>  $type->number_of_day ,
                                ];

                                $calculateEmpLeaveBalance2[] = [
                                    'title'        =>  'Balance',
                                    'balance'      =>  0 ,
                                ];
                            }
                        }
                        else
                        {
                            $calculateEmpLeaveBalance[] =[
                                'title'        =>  'No Leave Entitled',
                                'balance'      =>  0 ,
                            ];

                            $calculateEmpLeaveBalance2[] = [
                                'title'        =>  'No Leave Entitled',
                                'balance'      =>  0 ,
                            ];
                        }

                 }
                 else{
                     $calculateEmpLeaveBalance[] =[
                         'title'        =>  'No Leave Entitled',
                         'balance'      =>  0 ,
                     ];

                     $calculateEmpLeaveBalance2[] =[
                         'title'        =>  'No Leave Entitled',
                         'balance'      =>  0 ,
                     ];
                 }

            }

            $new['leaveTypeData']  = $calculateEmpLeaveBalance;
            $new['leaveYearData']  = $calculateEmpLeaveBalance2;

            return $calculateEmpLeaveBalance;

        }


    }

    function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

    function checkLeaveYearlyBalance(Request $request)
    {
        $leave_type_array = [];

        $leave_Status = 2;//2 == Approve

        $empId = $request->employee_id;

        $date = date('Y-m-d',strtotime($request->date));

        $calculateEmpLeaveBalance = [];

        $check_leave = HrApplyForLeave::select( DB::raw('SUM(number_of_day) as total_leave_enjoy'),'leave_type_id')
                                    ->where('employee_id',$empId)
                                    ->where('status',$leave_Status)
                                    ->whereYear('application_from_date',$date)
                                    ->groupBy('leave_type_id')
                                    ->orderBy('leave_type_id','ASC')
                                    ->get();

        $employeeTypeId = HrManageEmployee::select('employee_type_id')
            ->where('status','=',1)
            ->where('id',$empId)
            ->firstOrFail();

        if($check_leave->count() > 0)
        {
            foreach ($check_leave as $leave_with_type){

                $leaveTypeData = HrLeaveType::where('employee_type_id',$employeeTypeId->employee_type_id)
                                            ->where('leave_type_name',$leave_with_type->leave_type_id)
                                            ->first();

                array_push($leave_type_array,$leave_with_type->leave_type_id);

                $calculateEmpLeaveBalance[] =[

                    'leave_type_id'        =>  $leave_with_type->leave_type_id,
                    'title'        =>  'Balance',
                    'balance'      =>  $leaveTypeData->number_of_day - $leave_with_type->total_leave_enjoy ,

                ];

            }

            $leaveNotTypeDatas = HrLeaveType::where('employee_type_id',$employeeTypeId->employee_type_id)
                ->whereNotIn('leave_type_name', $leave_type_array)
                ->get();

            foreach ($leaveNotTypeDatas as $leaveNotTypeData)
            {
                $calculateEmpLeaveBalance[] =[

                    'leave_type_id'=>  $leaveNotTypeData->leave_type_name,
                    'title'        =>  'Balance',
                    'balance'      =>  $leaveNotTypeData->number_of_day,

                ];
            }

            $this->array_sort_by_column($calculateEmpLeaveBalance, 'leave_type_id');


        }
        else
        {

            $leaveTypeData = HrLeaveType::select('leave_type_name','number_of_day')
                                        ->where('employee_type_id',$employeeTypeId->employee_type_id)
                                        ->orderBy('leave_type_name','ASC')
                                        ->get();

            if($leaveTypeData->count() > 0)
            {
                foreach ($leaveTypeData as $type){

                    $calculateEmpLeaveBalance[] =[
                        'title'        =>  'Balance',
                        'balance'      =>  $type->number_of_day ,
                    ];
                }
            }
            else
            {
                $calculateEmpLeaveBalance[] =[
                    'title'        =>  'No Leave Entitled',
                    'balance'      =>  0 ,
                ];
            }

        }

        return $calculateEmpLeaveBalance;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id'           =>'required',
            'leave_type_id'         =>'required',
            'application_from_date' =>'required',
            'application_to_date'   =>'required',
            'number_of_day'         =>'required',
        ]);

        $input = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date']   = dateConvertFormtoDB($request->application_to_date);
        $input['created_by']= Auth::user()->id;

        try {
            DB::beginTransaction();

            HrApplyForLeave::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Leave Application successfully saved!']);
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
            $editModeData = HrApplyForLeave::FindOrFail($id);

            $data = [
                'id'                   => $editModeData->id,
                'employee_id'          => $editModeData->employee_id,
                'leave_type_id'        => $editModeData->leave_type_id,
                'number_of_day'        => $editModeData->number_of_day,
                'application_from_date'=> date('d-m-Y', strtotime($editModeData->application_from_date)) ,
                'application_to_date'  => date('d-m-Y', strtotime($editModeData->application_to_date)),
                'purpose'              => $editModeData->purpose,
                'check_status'         => $editModeData->check_status,
                'status'               => $editModeData->status
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_id'           =>'required',
            'leave_type_id'         =>'required',
            'application_from_date' =>'required',
            'application_to_date'   =>'required',
            'number_of_day'         =>'required',
        ]);

        $data = [
            'id'                   => $request->id,
            'employee_id'          => $request->employee_id,
            'leave_type_id'        => $request->leave_type_id,
            'number_of_day'        => $request->number_of_day,
            'purpose'              => $request->purpose,
            'application_from_date'=> dateConvertFormtoDB($request->application_from_date),
            'application_to_date'  => dateConvertFormtoDB($request->application_to_date),
            'check_status'         => $request->check_status,
            'status'               => $request->status
        ];


        try {
            DB::beginTransaction();

            $editModeData = HrApplyForLeave::FindOrFail($id);
            $editModeData->update($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Leave successfully updated!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrApplyForLeave::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Leave successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approvedEmpLeave($id){

        $approval = HrApplyForLeave::where('id',$id)->first()->status;

        if($approval ==1){
            HrApplyForLeave::where('id', $id)->update(['status' => 2]);// status =2 ; Approved
            return response()->json(['status' => 'success', 'message' => 'Successfully Approved!']);
        }
    }
}
