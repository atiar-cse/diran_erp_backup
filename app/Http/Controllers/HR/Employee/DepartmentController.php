<?php

namespace App\Http\Controllers\HR\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\HR\HrDepartment;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()){
            return HrDepartment::with('getPerson','getDiv')->orderBy('id','desc')
                ->paginate($request->perPage);
        }

        $headPersonList = getHeadPersonList();
        $divList = getDivList();

        return view('hr.employee_section.setup.department',compact('headPersonList','divList'));
    }



    public function store(Request $request)
    {
        $div_id = $request->div_id;
        $department_name = $request->department_name;

        $this->validate($request, [
            'div_id'=>'required',
            'department_name' => [
                'required',
                Rule::unique('hr_departments')->whereNull('deleted_at')->where(function ($query) use($div_id,$department_name) {
                    return $query->where('div_id', $div_id)
                        ->where('department_name', $department_name);
                }),
            ],
        ], [
            'div_id.required' => 'The Division field is required.',
            'department_name.required' => 'The Department Name field is required.',
            'department_name.unique' => 'The Department Name has already been taken for the Unit.',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;


        try {
            DB::beginTransaction();

            HrDepartment::create($input);

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
        return HrDepartment::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $div_id = $request->div_id;
        $department_name = $request->department_name;

        $this->validate($request, [
            'div_id'=>'required',
            'department_name' => [
                'required',
                Rule::unique('hr_departments')->ignore($id)->whereNull('deleted_at')->where(function ($query) use($div_id,$department_name) {
                    return $query->where('div_id', $div_id)
                        ->where('department_name', $department_name);
                }),
            ],
        ], [
            'div_id.required' => 'The Division field is required.',
            'department_name.required' => 'The Department Name field is required.',
            'department_name.unique' => 'The Department Name has already been taken for the Unit.',
        ]);

        $data = HrDepartment::findOrFail($id);
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
        $data = HrDepartment::FindOrFail($id);
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
