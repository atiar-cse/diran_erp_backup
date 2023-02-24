<?php

namespace App\Http\Controllers\Accounts\ChartOfAccounts;

use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsSubCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class AccountSubCodeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('accounts_sub_codes')
                ->leftJoin('accounts_main_codes', 'accounts_sub_codes.main_code_id', '=', 'accounts_main_codes.id')
                ->leftJoin('users as ura', 'accounts_sub_codes.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_sub_codes.updated_by','=','ured.id')
                ->whereNull('accounts_sub_codes.deleted_at')
                ->select(['accounts_sub_codes.id AS id',
                    'accounts_sub_codes.sub_code_title',
                    'accounts_sub_codes.sub_code',
                    'accounts_sub_codes.created_by',
                    'accounts_sub_codes.updated_by',
                    'accounts_sub_codes.status',
                    'accounts_main_codes.main_code_title',
                    'accounts_main_codes.main_code',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                ]);

            return datatables()->of($query)->toJson();
        }

        $accountMainCodeList      = AccountsMainCode::where('status', '1')->get();
        return view('accounts.chart_of_accounts.accounts_sub_code',compact('accountMainCodeList'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'main_code_id'=>'required',
            'sub_code_title'=>'required|unique:accounts_sub_codes,sub_code_title',
            'sub_code'=>'required|unique:accounts_sub_codes,sub_code',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            AccountsSubCode::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Accounts Sub Code successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Accounts Sub Code is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }




    public function edit($id)
    {
        return AccountsSubCode::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'main_code_id'=>'required',
            'sub_code_title'=>'required',
            'sub_code'=>'required',
        ]);

        $data = AccountsSubCode::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Accounts Sub Code successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Accounts Sub Code is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = AccountsSubCode ::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Accounts Sub Code successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
