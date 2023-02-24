<?php

namespace App\Http\Controllers\HR\Attendance;

use App\Model\HR\HrManageWorkShift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class ManageWorkShiftController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrManageWorkShift::orderBy('id','desc')->paginate($request->perPage);
        }
        return view('hr.attendance_section.manage_work_shift');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'attn_date'      =>'required',

        ]);

        $process_date = date("d-m-Y",strtotime(str_replace('/','-',$request->attn_date)));

        $DB = DB::connection('odbc')->table('CHECKINOUT')->get();

        print_r($DB);

        exit();
        /*$this->validate($request, [
            'shift_name'      =>'required',
            'start_time'      =>'required',
            'end_time'        =>'required',
            'late_count_time' =>'required',

        ]);

        $input = $request->all();
        $input['start_time']       = date("H:i:s", strtotime($request->start_time));
        $input['end_time']         = date("H:i:s", strtotime($request->end_time));
        $input['late_count_time']  = date("H:i:s", strtotime($request->late_count_time));
        $input['created_by']       = Auth::user()->id;

        try {
            DB::beginTransaction();

            HrManageWorkShift::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Employee Work Shift successfully saved!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }*/
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {

        try {
            $editModeData = HrManageWorkShift::FindOrFail($id);

            $data = [
                'id'              => $editModeData->id,
                'shift_name'      => $editModeData->shift_name,
                'start_time'      => date("g:i A", strtotime($editModeData->start_time)),
                'end_time'        => date("g:i A", strtotime($editModeData->end_time)),
                'late_count_time' => date("g:i A", strtotime($editModeData->late_count_time)),
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'shift_name'      =>'required',
            'start_time'      =>'required',
            'end_time'        =>'required',
            'late_count_time' =>'required',

        ]);

        $data = [
            'shift_name'          => $request->shift_name,
            'start_time'          => date("H:i:s", strtotime($request->start_time)),
            'end_time'            => date("H:i:s", strtotime($request->end_time)),
            'late_count_time'     => date("H:i:s", strtotime($request->late_count_time)),
            'updated_by'          => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $editModeData = HrManageWorkShift::FindOrFail($id);
            $editModeData->update($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Employee Work Shift data successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Employee Work Shift Grade data is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrManageWorkShift::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Employee Work Shift successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
