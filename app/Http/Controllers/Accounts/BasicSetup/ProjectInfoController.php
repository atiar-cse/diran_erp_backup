<?php

namespace App\Http\Controllers\Accounts\BasicSetup;

use App\Model\Accounts\AccountsProjectInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class ProjectInfoController extends Controller
{

    public function index(Request $request)
    {

        if ($request->filled('token_project') && $request->token_project == 'token_project') {
            $project = AccountsProjectInfo::where('status',1)->get();
            return  $project;
        }

        if($request->ajax()){
            $query = DB::table('accounts_project_infos')
                ->leftJoin('users as ura', 'accounts_project_infos.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_project_infos.updated_by','=','ured.id')
                ->whereNull('accounts_project_infos.deleted_at')
                ->select(['accounts_project_infos.id AS id',
                    'accounts_project_infos.project_name',
                    'accounts_project_infos.remarks',
                    'accounts_project_infos.created_by',
                    'accounts_project_infos.updated_by',
                    'accounts_project_infos.status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                ]);

            return datatables()->of($query)->toJson();
        }

        return view('accounts.accounts_setup.project_info');
    }


    public function store_project(Request $request)
    {
        $this->validate($request, [
            'project_name'=>'required|unique:accounts_project_infos,project_name',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();
            $pr =AccountsProjectInfo::create($input);
            DB::commit();

            $list = AccountsProjectInfo::where('status',1)->get();
            $pro_id = $pr->id;

            $data = [
                'project_id'    =>  $pro_id,
                'project_list'  =>  $list
            ];

            return response()->json($data);
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
            if ($bug == 1062) {
                return response()->json(['status' => 'error', 'message' => 'Project is Found Duplicate']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
            }
        }


    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'project_name'=>'required|unique:accounts_project_infos,project_name',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();
            AccountsProjectInfo::create($input);
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Project successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Project is Found Duplicate']);
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
        return AccountsProjectInfo::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'project_name'=>'required',
        ]);

        $data = AccountsProjectInfo::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Project successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Project is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = AccountsProjectInfo::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Project Info successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
