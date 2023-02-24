<?php

namespace App\Http\Controllers\HR\Leave;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\HR\HrManageHoliday;
use Illuminate\Support\Facades\Auth;
use DB;

class ManageHolidayController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return HrManageHoliday::orderBy('id','desc')->paginate($request->perPage);
        }
        return view('hr.leave_section.manage_holiday');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'holiday_name'=>'required|unique:hr_manage_holidays,holiday_name',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        try {
            DB::beginTransaction();

            HrManageHoliday::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Manage Holiday successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Manage Holiday is Found Duplicate']);
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
        return HrManageHoliday::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'holiday_name'=>'required',
        ]);

        $data = HrManageHoliday::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Manage Holiday successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Manage Holiday is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrManageHoliday::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Manage Holiday successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
