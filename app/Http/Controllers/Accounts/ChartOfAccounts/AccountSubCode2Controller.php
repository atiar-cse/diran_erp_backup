<?php

namespace App\Http\Controllers\Accounts\ChartOfAccounts;

use App\Model\Accounts\AccountsSubCode;
use App\Model\Accounts\AccountsSubCode2;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class AccountSubCode2Controller extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $query = DB::table('accounts_sub_code2s')
                ->leftJoin('accounts_sub_codes', 'accounts_sub_code2s.sub_code_id', '=', 'accounts_sub_codes.id')
                ->leftJoin('users as ura', 'accounts_sub_code2s.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_sub_code2s.updated_by','=','ured.id')
                ->whereNull('accounts_sub_code2s.deleted_at')
                ->select(['accounts_sub_code2s.id AS id',
                    'accounts_sub_code2s.sub_code_title2',
                    'accounts_sub_code2s.sub_code2',
                    'accounts_sub_code2s.created_by',
                    'accounts_sub_code2s.updated_by',
                    'accounts_sub_code2s.status',
                    'accounts_sub_codes.sub_code_title',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                ]);

            return datatables()->of($query)->toJson();

        }

        $subCodeList      = AccountsSubCode::where('status', '1')->get();
        return view('accounts.chart_of_accounts.accounts_sub_code2',compact('subCodeList'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'sub_code_id'=>'required',
            'sub_code_title2'=>'required|unique:accounts_sub_code2s,sub_code_title2',
            'sub_code2'=>'required|unique:accounts_sub_code2s,sub_code2',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            AccountsSubCode2::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Accounts Sub Code2 successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Accounts Sub Code2 is Found Duplicate']);
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
        return AccountsSubCode2::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'sub_code_id'=>'required',
            'sub_code_title2'=>'required',
            'sub_code2'=>'required',
        ]);

        $data = AccountsSubCode2::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Accounts Sub Code2 successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Accounts Sub Code2 is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = AccountsSubCode2 ::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Accounts Sub Code2 successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
