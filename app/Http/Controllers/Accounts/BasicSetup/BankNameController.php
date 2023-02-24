<?php

namespace App\Http\Controllers\Accounts\BasicSetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsBankName;
use Illuminate\Support\Facades\Auth;
use DB;

class BankNameController extends Controller
{

    public function index(Request $request)
    {
        //return datatables()->of(AccountsBankName::query())->toJson();
        if ($request->ajax()) {
            $query = DB::table('accounts_bank_names')
                ->leftJoin('users as ura', 'accounts_bank_names.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'accounts_bank_names.updated_by', '=', 'ured.id')
                ->whereNull('accounts_bank_names.deleted_at')
                ->select(['accounts_bank_names.id AS id',
                    'accounts_bank_names.accounts_bank_names',
                    'accounts_bank_names.accounts_bank_codes',
                    'accounts_bank_names.accounts_bank_address',
                    'accounts_bank_names.created_by',
                    'accounts_bank_names.updated_by',
                    'accounts_bank_names.status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                ]);

            return datatables()->of($query)->toJson();
        }
    }


    public function create()
    {

        return view('accounts.accounts_setup.bank_name');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'accounts_bank_names'=>'required|unique:accounts_bank_names,accounts_bank_names',
            'accounts_bank_codes'=>'required|unique:accounts_bank_names,accounts_bank_codes',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        //print_r($input);
        //exit;

        try {
            DB::beginTransaction();
            AccountsBankName::create($input);
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Bank Name successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Bank Name is Found Duplicate']);
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
        return AccountsBankName::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'accounts_bank_names'=>'required|unique:accounts_bank_names,accounts_bank_names,'.$id.',id',
            'accounts_bank_codes'=>'required|unique:accounts_bank_names,accounts_bank_codes,'.$id.',id',
            'status'=>'required',
        ]);

        $data = AccountsBankName::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Bank Name successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Bank Name is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = AccountsBankName ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Bank Name successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
