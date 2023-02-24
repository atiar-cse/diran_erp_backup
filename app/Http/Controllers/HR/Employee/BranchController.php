<?php

namespace App\Http\Controllers\HR\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\HR\HrBranch;
use Illuminate\Support\Facades\Auth;
use DB;

class BranchController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return HrBranch::orderBy('id','desc')->paginate($request->perPage);
        }
        return view('hr.employee_section.branch');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'branch_name'=>'required|unique:hr_branches,branch_name',
            'address'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;


        try {
            DB::beginTransaction();

            HrBranch::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Branch successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Branch is Found Duplicate']);
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
        return HrBranch::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'branch_name'=>'required',
            'address'=>'required',
        ]);


        $data = HrBranch::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Branch successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Branch is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrBranch::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Branch successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
