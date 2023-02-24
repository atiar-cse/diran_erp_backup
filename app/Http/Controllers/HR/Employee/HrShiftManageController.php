<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrShiftManage;
use App\Model\HR\HrShiftSchedule;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrShiftManageController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrShiftManage::with('get_shift_schedule')->orderBy('id', 'desc')->paginate($request->perPage);
        }

        $shift_schedule_list = HrShiftSchedule::orderBy('id', 'desc')->where('status', 1)->get();
        return view('hr.employee_section.setup.shift_manage', compact('shift_schedule_list'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'shift_name' => 'required|unique:hr_shift_manage,shift_name',
            'shift_schedule_id' => 'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        $input['created_at'] = Carbon::now();


        try {
            DB::beginTransaction();

            HrShiftManage::create($input);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Department successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Department is Found Duplicate']);
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
        return HrShiftManage::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'shift_name' => 'required',
            'shift_schedule_id' => 'required',
        ]);

        $data = HrShiftManage::findOrFail($id);
        $input = $request->all();

        $input['updated_by'] = Auth::user()->id;
        $input['updated_at'] = Carbon::now();

        try {
            DB::beginTransaction();

            $data->update($input);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Department successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Department is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrShiftManage::FindOrFail($id);
        try {

            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Department successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
