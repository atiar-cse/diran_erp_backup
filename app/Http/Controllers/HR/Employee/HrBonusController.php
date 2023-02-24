<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrBonus;
use App\Model\HR\HrEmployeeType;
use App\Model\HR\HrUnit;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrBonusController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrBonus::with('get_unit', 'get_employee_type')->orderBy('id', 'desc')->paginate($request->perPage);
        }

        $unit_list = HrUnit::orderBy('id', 'desc')->where('status', 1)->get();
        $employee_type_list = HrEmployeeType::orderBy('id', 'desc')->where('status', 1)->get();
        return view('hr.employee_section.setup.bonus', compact('unit_list', 'employee_type_list'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:hr_bonus,title',
            'unit_id' => 'required',
            'employee_type' => 'required',
            'start_month' => 'required',
            'end_month' => 'required',
            'salary_type' => 'required',
            'amount_percent' => 'required',
            'bonus_percent' => 'required',
            'effective_date' => 'required',
        ]);

        $input = $request->all();
        $input['effective_date'] = dateConvertFormtoDB($request->effective_date);
        $input['created_by'] = Auth::user()->id;
        $input['created_at'] = Carbon::now();

        try {
            DB::beginTransaction();

            HrBonus::create($input);

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
        return HrBonus::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'unit_id' => 'required',
            'employee_type' => 'required',
            'start_month' => 'required',
            'end_month' => 'required',
            'salary_type' => 'required',
            'amount_percent' => 'required',
            'bonus_percent' => 'required',
            'effective_date' => 'required',
        ]);

        $data = HrBonus::findOrFail($id);

        $input = $request->all();
        $input['effective_date'] = dateConvertFormtoDB($request->effective_date);
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
        $data = HrBonus::FindOrFail($id);
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
