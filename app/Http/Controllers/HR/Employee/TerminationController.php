<?php

namespace App\Http\Controllers\HR\Employee;

use App\Model\HR\HrManageEmployee;
use App\Model\HR\HrTermination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class TerminationController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrTermination::with('employee_terminated','terminated_by_employee')->orderBy('id','desc')->paginate($request->perPage);
        }

        $employeeList = HrManageEmployee::where('status', '0')->get();
        return view('hr.employee_section.termination',compact('employeeList'));
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_terminated_employee_id'=>'required',
            'termination_types'              =>'required',
            'subject'                        =>'required',
            'terminated_by_employee_id'      =>'required',
            'notice_date'                    =>'required',
            'termination_date'               =>'required',
        ]);

        $data = [
            'employee_terminated_employee_id' => $request->employee_terminated_employee_id,
            'termination_types'               => $request->termination_types,
            'subject'                         => $request->subject,
            'terminated_by_employee_id'       => $request->terminated_by_employee_id,
            'notice_date'                     => dateConvertFormtoDB($request->notice_date),
            'termination_date'                => dateConvertFormtoDB($request->termination_date),
            'narration'                       => $request->narration,
            'created_by'                      => Auth::user()->id,
        ];

        $empID = $request->employee_terminated_employee_id;

        if($empID !=''){
            HrManageEmployee::where('id', $empID)->update(['permanent_status' => 5]);
        }

        try {
            DB::beginTransaction();

            HrTermination::create($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Termination successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Termination is Found Duplicate']);
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
            $editModeData = HrTermination::FindOrFail($id);
            $data = [
                'id'                              => $editModeData->id,
                'employee_terminated_employee_id' => $editModeData->employee_terminated_employee_id,
                'termination_types'               => $editModeData->termination_types,
                'subject'                         => $editModeData->subject,
                'terminated_by_employee_id'       => $editModeData->terminated_by_employee_id,
                'notice_date'                     => dateConvertDBtoForm($editModeData->notice_date),
                'termination_date'                => dateConvertDBtoForm($editModeData->termination_date),
                'narration'                       => $editModeData->narration,
                'status'                          => $editModeData->status
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_terminated_employee_id'=>'required',
            'termination_types'              =>'required',
            'subject'                        =>'required',
            'terminated_by_employee_id'      =>'required',
            'notice_date'                    =>'required',
            'termination_date'               =>'required',
        ]);

        $data = [
            'employee_terminated_employee_id' => $request->employee_terminated_employee_id,
            'termination_types'               => $request->termination_types,
            'subject'                         => $request->subject,
            'terminated_by_employee_id'       => $request->terminated_by_employee_id,
            'notice_date'                     => dateConvertFormtoDB($request->notice_date),
            'termination_date'                => dateConvertFormtoDB($request->termination_date),
            'narration'                       => $request->narration,
            'created_by'                      => Auth::user()->id,
        ];
        try {
            DB::beginTransaction();

            $editModeData = HrTermination::FindOrFail($id);
            $editModeData->update($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Termination successfully updated!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrTermination::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Termination successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
