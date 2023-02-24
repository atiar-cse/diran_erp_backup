<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use App\Model\HR\HrTeam;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class HrTeamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrTeam::with('getPerson','getDept')->orderBy('id', 'desc')
                ->paginate($request->perPage);
        }

        $headPersonList = getHeadPersonList();
        $deptList = getDeptList();

        return view('hr.employee_section.setup.team', compact('headPersonList','deptList'));
    }

    public function store(Request $request)
    {
        $dept_id = $request->dept_id;
        $title = $request->title;

        $this->validate($request, [
            'dept_id'=>'required',
            'title' => [
                'required',
                Rule::unique('hr_teams')->whereNull('deleted_at')->where(function ($query) use($dept_id,$title) {
                    return $query->where('dept_id', $dept_id)
                        ->where('title', $title);
                }),
            ],
        ], [
            'dept_id.required' => 'The Department field is required.',
            'title.required' => 'The Team Name field is required.',
            'title.unique' => 'The Team Name has already been taken for the Department.',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();
                HrTeam::create($input);
            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Team successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Team is Found Duplicate']);
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
        return HrTeam::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $dept_id = $request->dept_id;
        $title = $request->title;

        $this->validate($request, [
            'dept_id'=>'required',
            'title' => [
                'required',
                Rule::unique('hr_teams')->ignore($id)->whereNull('deleted_at')->where(function ($query) use($dept_id,$title) {
                    return $query->where('dept_id', $dept_id)
                        ->where('title', $title);
                }),
            ],
        ], [
            'dept_id.required' => 'The Department field is required.',
            'title.required' => 'The Team Name field is required.',
            'title.unique' => 'The Team Name has already been taken for the Department.',
        ]);

        $data = HrTeam::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Team successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Team is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = HrTeam::FindOrFail($id);
        try {

            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Team successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
