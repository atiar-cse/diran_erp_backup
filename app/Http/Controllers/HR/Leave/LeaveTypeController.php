<?php

namespace App\Http\Controllers\HR\Leave;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\HR\HrLeaveType;
use Illuminate\Support\Facades\Auth;
use DB;


class LeaveTypeController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return HrLeaveType::with('getEmployeeType')->orderBy('id','desc')->paginate($request->perPage);
        }
        $typeList  = getEmpTypeList();
        return view('hr.leave_section.leave_type',compact('typeList'));
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'leave_type_name'=>'required',
            'number_of_day'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            HrLeaveType::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Leave Type successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Leave Type is Found Duplicate']);
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
        return HrLeaveType::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'leave_type_name'=>'required',
            'number_of_day'=>'required',
        ]);

        $data = HrLeaveType::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Leave Type successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Leave Type is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrLeaveType::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Leave Type successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
