<?php

namespace App\Http\Controllers\HR\Employee;

use App\Model\HR\HrDivision;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Validation\Rule;


class HrDivisionController extends Controller
{


    public function index(Request $request)
    {
        if($request->ajax()){
            return HrDivision::with('getPerson','getUnit')->orderBy('id','desc')
                ->paginate($request->perPage);
        }

        $headPersonList = getHeadPersonList();
        $unitList = getUnitList();

        return view('hr.employee_section.setup.division',compact('headPersonList','unitList'));
    }


    public function store(Request $request)
    {
        $unit_id = $request->unit_id;
        $title = $request->title;

        $this->validate($request, [
            'unit_id'=>'required',
            'title' => [
                'required',
                Rule::unique('hr_divisions')->whereNull('deleted_at')->where(function ($query) use($unit_id,$title) {
                    return $query->where('unit_id', $unit_id)
                        ->where('title', $title);
                }),
            ],
        ], [
            'unit_id.required' => 'The Unit field is required.',
            'title.required' => 'The Division Name field is required.',
            'title.unique' => 'The Division Name has already been taken for the Unit.',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();
                HrDivision::create($input);
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Division successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Division is Found Duplicate']);
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
        return HrDivision::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $unit_id = $request->unit_id;
        $title = $request->title;

        $this->validate($request, [
            'unit_id'=>'required',
            'title' => [
                'required',
                Rule::unique('hr_divisions')->ignore($id)->whereNull('deleted_at')->where(function ($query) use($unit_id,$title) {
                    return $query->where('unit_id', $unit_id)
                        ->where('title', $title);
                }),
            ],
        ], [
            'unit_id.required' => 'The Unit field is required.',
            'title.required' => 'The Division Name field is required.',
            'title.unique' => 'The Division Name has already been taken for the Unit.',
        ]);

        $data = HrDivision::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Division successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Division is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrDivision::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Division successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
