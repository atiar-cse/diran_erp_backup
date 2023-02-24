<?php

namespace App\Http\Controllers\Accounts\ChartOfAccounts;

use App\Model\Accounts\AccountsMainCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class AccountMainCodeController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return AccountsMainCode::orderBy('id','desc')->paginate($request->perPage);
        }

        return view('accounts.chart_of_accounts.accounts_main_code');

    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'main_code_title'=>'required|unique:accounts_main_codes,main_code_title',
            'main_code'=>'required|unique:accounts_main_codes,main_code',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            AccountsMainCode::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Accounts Main Code successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Accounts Main Code is Found Duplicate']);
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
        return AccountsMainCode::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'main_code_title'=>'required',
            'main_code'=>'required',
        ]);

        $data = AccountsMainCode::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Accounts Main Code successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Accounts Main Code is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = AccountsMainCode ::FindOrFail($id);
        try{

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Accounts Main Code successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
