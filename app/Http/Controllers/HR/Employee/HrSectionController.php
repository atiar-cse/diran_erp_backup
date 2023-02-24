<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrSection;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HrSectionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrSection::with('getUnit')->orderBy('id', 'desc')->paginate($request->perPage);
        }

        $unitList = getUnitList();
        return view('hr.employee_section.setup.section',compact('unitList'));
    }


    public function store(Request $request)
    {
        $unit_id = $request->unit_id;
        $section_name = $request->section_name;

        $this->validate($request, [
            'unit_id'=>'required',
            'section_name' => [
                'required',
                Rule::unique('hr_sections')->whereNull('deleted_at')->where(function ($query) use($unit_id,$section_name) {
                    return $query->where('unit_id', $unit_id)
                        ->where('section_name', $section_name);
                }),
            ],
        ], [
            'unit_id.required' => 'The Unit field is required.',
            'section_name.required' => 'The Section Name field is required.',
            'section_name.unique' => 'The Section Name has already been taken for the Unit.',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();
                HrSection::create($input);
            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Section successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Section is Found Duplicate']);
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
        return HrSection::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $unit_id = $request->unit_id;
        $section_name = $request->section_name;

        $this->validate($request, [
            'unit_id'=>'required',
            'section_name' => [
                'required',
                Rule::unique('hr_sections')->ignore($id)->whereNull('deleted_at')->where(function ($query) use($unit_id,$section_name) {
                    return $query->where('unit_id', $unit_id)
                        ->where('section_name', $section_name);
                }),
            ],
        ], [
            'unit_id.required' => 'The Unit field is required.',
            'section_name.required' => 'The Section Name field is required.',
            'section_name.unique' => 'The Section Name has already been taken for the Unit.',
        ]);

        $data = HrSection::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Section successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Section is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = HrSection::FindOrFail($id);
        try {

            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Section successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
