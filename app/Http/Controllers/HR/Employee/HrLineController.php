<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrLine;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HrLineController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrLine::with('getUnit')->orderBy('id', 'desc')->paginate($request->perPage);
        }

        $unitList = getUnitList();
        return view('hr.employee_section.setup.line',compact('unitList'));
    }


    public function store(Request $request)
    {
        $unit_id = $request->unit_id;
        $line_name = $request->line_name;

        $this->validate($request, [
            'unit_id'=>'required',
            'line_name' => [
                'required',
                Rule::unique('hr_lines')->whereNull('deleted_at')->where(function ($query) use($unit_id,$line_name) {
                    return $query->where('unit_id', $unit_id)
                        ->where('line_name', $line_name);
                }),
            ],
        ], [
            'unit_id.required' => 'The Unit field is required.',
            'line_name.required' => 'The Line Name field is required.',
            'line_name.unique' => 'The Line Name has already been taken for the Unit.',
        ]);


        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            HrLine::create($input);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Line successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Line is Found Duplicate']);
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
        return HrLine::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $unit_id = $request->unit_id;
        $line_name = $request->line_name;

        $this->validate($request, [
            'unit_id'=>'required',
            'line_name' => [
                'required',
                Rule::unique('hr_lines')->ignore($id)->whereNull('deleted_at')->where(function ($query) use($unit_id,$line_name) {
                    return $query->where('unit_id', $unit_id)
                        ->where('line_name', $line_name);
                }),
            ],
        ], [
            'unit_id.required' => 'The Unit field is required.',
            'line_name.required' => 'The Line Name field is required.',
            'line_name.unique' => 'The Line Name has already been taken for the Unit.',
        ]);

        $data = HrLine::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Line successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Line is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = HrLine::FindOrFail($id);
        try {

            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Line successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
