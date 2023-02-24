<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrDesignation;
use App\Model\HR\HrEmployeeLevel;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrDesignation::with('getEmployeeLevel')->orderBy('id', 'desc')->paginate($request->perPage);
        }
        $employee_level_list = HrEmployeeLevel::where('status', '1')->get();

        return view('hr.employee_section.setup.designation', compact('employee_level_list'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'designation_name' => 'required|unique:hr_designations,designation_name',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            HrDesignation::create($input);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Designation successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Designation is Found Duplicate']);
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
        return HrDesignation::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'designation_name' => 'required',
        ]);

        $data = HrDesignation::findOrFail($id);
        $input = $request->all();
        $input['updated_by'] = Auth::user()->id;

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
            return response()->json(['status' => 'success', 'message' => 'Designation successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Designation is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = HrDesignation::FindOrFail($id);
        try {

            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Designation successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
