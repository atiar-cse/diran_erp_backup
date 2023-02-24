<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrShiftSchedule;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrShiftScheduleController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrShiftSchedule::orderBy('id', 'desc')->paginate($request->perPage);
        }
        return view('hr.employee_section.setup.shift_schedule');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:hr_shift_schedule',
            'in_start' => 'required',
            'in_time' => 'required',
            'late_start' => 'required',
            'in_end' => 'required',
            'out_start' => 'required',
            'out_end' => 'required',
        ]);

        $input = $request->all();

        $input['in_start'] = date("H:i:s", strtotime($request->in_start));
        $input['in_time'] = date("H:i:s", strtotime($request->in_time));
        $input['late_start'] = date("H:i:s", strtotime($request->late_start));
        $input['in_end'] = date("H:i:s", strtotime($request->in_end));
        $input['out_start'] = date("H:i:s", strtotime($request->out_start));
        $input['out_end'] = date("H:i:s", strtotime($request->out_end));

        $input['created_by'] = Auth::user()->id;
        $input['created_at'] = Carbon::now();

        try {
            DB::beginTransaction();

            HrShiftSchedule::create($input);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Shift schedule successfully saved!']);
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
            $editModeData = HrShiftSchedule::FindOrFail($id);

            $data = [
                'id' => $editModeData->id,
                'title' => $editModeData->title,
                'in_start' => date("g:i A", strtotime($editModeData->in_start)),
                'in_time' => date("g:i A", strtotime($editModeData->in_time)),
                'late_start' => date("g:i A", strtotime($editModeData->late_start)),
                'in_end' => date("g:i A", strtotime($editModeData->in_end)),
                'out_start' => date("g:i A", strtotime($editModeData->out_start)),
                'out_end' => date("g:i A", strtotime($editModeData->out_end)),
                'status' => $editModeData->status,
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'in_start' => 'required',
            'in_time' => 'required',
            'late_start' => 'required',
            'in_end' => 'required',
            'out_start' => 'required',
            'out_end' => 'required',
        ]);

        $data = [
            'title' => $request->title,
            'in_start' => date("H:i:s", strtotime($request->in_start)),
            'in_time' => date("H:i:s", strtotime($request->in_time)),
            'late_start' => date("H:i:s", strtotime($request->late_start)),
            'in_end' => date("H:i:s", strtotime($request->in_end)),
            'out_start' => date("H:i:s", strtotime($request->out_start)),
            'out_end' => date("H:i:s", strtotime($request->out_end)),
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
            'status' => $request->status,
        ];

        try {
            DB::beginTransaction();

            $editModeData = HrShiftSchedule::FindOrFail($id);
            $editModeData->update($data);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
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
        $data = HrShiftSchedule::FindOrFail($id);
        try {
            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
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
