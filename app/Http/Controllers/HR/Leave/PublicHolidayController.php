<?php

namespace App\Http\Controllers\HR\Leave;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\HR\HrManageHoliday;
use App\Model\HR\HrPublicHoliday;
use Illuminate\Support\Facades\Auth;
use DB;

class PublicHolidayController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            return HrPublicHoliday::Select('hr_public_holidays.*')
                ->orderBy('hr_public_holidays.holiday_name','asc')->paginate($request->perPage);
        }

        $holidayLists   = HrManageHoliday::where('status', '1')->get();
        return view('hr.leave_section.public_holiday',compact('holidayLists'));
    }




    public function store(Request $request)
    {
        $this->validate($request, [
            'holiday_name'=>'required',
        ]);

        $input = $request->all();

        $input['from_date']= dateConvertFormtoDB($request->from_date);
        $input['to_date']  = dateConvertFormtoDB($request->to_date);
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            HrPublicHoliday::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Public Holiday successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Public Holiday is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        return HrPublicHoliday::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'holiday_name'=>'required',
        ]);

        $data = HrPublicHoliday::findOrFail($id);

        $input = $request->all();
        $input['updated_by'] = Auth::user()->id;
        $input['from_date']  = dateConvertFormtoDB($request->from_date);
        $input['to_date']    = dateConvertFormtoDB($request->to_date);

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
            return response()->json(['status' => 'success', 'message' => 'Public Holiday successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Public Holiday is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrPublicHoliday::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Public Holiday successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
