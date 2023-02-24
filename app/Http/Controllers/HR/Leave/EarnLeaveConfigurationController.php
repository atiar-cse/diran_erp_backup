<?php

namespace App\Http\Controllers\HR\Leave;

use App\Model\HR\HrEarnLeaveConfiguration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class EarnLeaveConfigurationController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return HrEarnLeaveConfiguration::orderBy('id','desc')->paginate($request->perPage);
        }
        return view('hr.leave_section.earn_leave_configuration');
    }



    public function store(Request $request)
    {
        /*$this->validate($request, [
            'day_of_earn_leave'=>'required',
        ]);*/

        $input = $request->all();
        //dd($input);
        $input['created_by'] = Auth::user()->id;


        try {
            DB::beginTransaction();

            HrEarnLeaveConfiguration::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
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
        return HrEarnLeaveConfiguration::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'day_of_earn_leave'=>'required',
        ]);

        $data = HrEarnLeaveConfiguration::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Department successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Department is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrEarnLeaveConfiguration::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
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
