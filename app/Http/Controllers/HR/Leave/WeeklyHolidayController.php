<?php

namespace App\Http\Controllers\HR\Leave;

use App\Model\HR\HrWeeklyHoliday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class WeeklyHolidayController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrWeeklyHoliday::where('status','=',1)->orderBy('id','desc')->paginate($request->perPage);
        }
        return view('hr.leave_section.weekly_holiday');
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'weekly_holiday_name'=>'required|unique:hr_weekly_holidays,weekly_holiday_name',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            HrWeeklyHoliday::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Weekly Holiday successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Weekly Holiday is Found Duplicate']);
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
        return HrWeeklyHoliday::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'weekly_holiday_name'=>'required',
        ]);

        $data = HrWeeklyHoliday::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Weekly Holiday successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Weekly Holiday is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrWeeklyHoliday::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Weekly Holiday successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
