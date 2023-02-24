<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrFloor;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HrFloorController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrFloor::with('getUnit')->orderBy('id', 'desc')->paginate($request->perPage);
        }

        $unitList = getUnitList();
        return view('hr.employee_section.setup.floor',compact('unitList'));
    }


    public function store(Request $request)
    {
        $unit_id = $request->unit_id;
        $floor_name = $request->floor_name;

        $this->validate($request, [
            'unit_id'=>'required',
            'floor_name' => [
                'required',
                Rule::unique('hr_floors')->whereNull('deleted_at')->where(function ($query) use($unit_id,$floor_name) {
                    return $query->where('unit_id', $unit_id)
                        ->where('floor_name', $floor_name);
                }),
            ],
        ], [
            'unit_id.required' => 'The Unit field is required.',
            'floor_name.required' => 'The Floor Name field is required.',
            'floor_name.unique' => 'The Floor Name has already been taken for the Unit.',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();
                HrFloor::create($input);
            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Floor successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Floor is Found Duplicate']);
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
        return HrFloor::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $unit_id = $request->unit_id;
        $floor_name = $request->floor_name;

        $this->validate($request, [
            'unit_id'=>'required',
            'floor_name' => [
                'required',
                Rule::unique('hr_floors')->ignore($id)->whereNull('deleted_at')->where(function ($query) use($unit_id,$floor_name) {
                    return $query->where('unit_id', $unit_id)
                        ->where('floor_name', $floor_name);
                }),
            ],
        ], [
            'unit_id.required' => 'The Unit field is required.',
            'floor_name.required' => 'The Floor Name field is required.',
            'floor_name.unique' => 'The Floor Name has already been taken for the Unit.',
        ]);


        $data = HrFloor::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Floor successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Floor is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = HrFloor::FindOrFail($id);
        try {

            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Floor successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
