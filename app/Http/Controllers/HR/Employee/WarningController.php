<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrManageEmployee;
use App\Model\HR\HrWarning;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;

class WarningController extends Controller
{

    public function index(Request $request)
    {

        if ($request->ajax()) {
            return HrWarning::with('warning_to_employee','warning_by_employee')->orderBy('id','desc')->paginate($request->perPage);
        }

        $employeeList = HrManageEmployee::where('status', '1')->get();

        return view('hr.employee_section.warning',compact('employeeList'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'warning_to_employee_id'=>'required',
            'warning_types'=>'required',
            'subject'=>'required',
            'warning_by_employee_id'=>'required',
            'warning_date'=>'required',
        ]);

        $data = [
            'warning_to_employee_id' => $request->warning_to_employee_id,
            'warning_types'          => $request->warning_types,
            'subject'                => $request->subject,
            'warning_by_employee_id' => $request->warning_by_employee_id,
            'warning_date'           => dateConvertFormtoDB($request->warning_date),
            'narration'              => $request->narration,
            'created_by'             => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            HrWarning::create($data);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Warning successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Warning is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }


    }


    public function show($id)
    {

    }


    public function edit($id)
    {

        try {
            $editModeData = HrWarning::FindOrFail($id);
            $data = [
                'id'                     => $editModeData->id,
                'warning_to_employee_id' => $editModeData->warning_to_employee_id,
                'warning_types'          => $editModeData->warning_types,
                'subject'                => $editModeData->subject,
                'warning_by_employee_id' => $editModeData->warning_by_employee_id,
                'warning_date'           => dateConvertDBtoForm($editModeData->warning_date),
                'narration'              => $editModeData->narration,
                'status'                 => $editModeData->status
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch (Exception $e) {
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'warning_to_employee_id'=>'required',
            'warning_types'=>'required',
            'subject'=>'required',
            'warning_by_employee_id'=>'required',
            'warning_date'=>'required',
        ]);

        $data = [
            'warning_to_employee_id' => $request->warning_to_employee_id,
            'warning_types'          => $request->warning_types,
            'subject'                => $request->subject,
            'warning_by_employee_id' => $request->warning_by_employee_id,
            'warning_date'           => dateConvertFormtoDB($request->warning_date),
            'narration'              => $request->narration,
            'updated_by'             => Auth::user()->id,
        ];


        try {
            DB::beginTransaction();

            $editModeData = HrWarning::FindOrFail($id);
            $editModeData->update($data);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Warning Section successfully updated!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrWarning::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Warning Agnet successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
