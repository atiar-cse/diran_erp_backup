<?php

namespace App\Http\Controllers\Accounts\BasicSetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsBankName;
use App\Model\Accounts\AccountsBankBranch;
use Illuminate\Support\Facades\Auth;
use DB;

class BankBranchController extends Controller
{

    public function index(Request $request){

        if ($request->ajax()) {
            $query = DB::table('accounts_bank_branches')
                ->leftJoin('accounts_bank_names', 'accounts_bank_branches.bank_id', '=', 'accounts_bank_names.id')
                ->leftJoin('users as ura', 'accounts_bank_branches.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'accounts_bank_branches.updated_by', '=', 'ured.id')
                ->whereNull('accounts_bank_branches.deleted_at')
                ->select(['accounts_bank_branches.id AS id',
                    'accounts_bank_branches.bank_branch_names',
                    'accounts_bank_branches.bank_branch_codes',
                    'accounts_bank_branches.created_by',
                    'accounts_bank_branches.bank_branch_contact_no',
                    'accounts_bank_branches.bank_branch_address',
                    'accounts_bank_branches.status',
                    'accounts_bank_names.accounts_bank_names',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                ]);

            return datatables()->of($query)->toJson();
        }

    }


    public function create()
    {
        $bankLists   = AccountsBankName::where('status', '1')->get();
        return view('accounts.accounts_setup.bank_branch',compact('bankLists'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'bank_branch_names'=>'required|unique:accounts_bank_branches,bank_branch_names',
            'bank_branch_codes'=>'required|unique:accounts_bank_branches,bank_branch_codes',
            'bank_id'=>'required',
            'status'=>'required',
        ]);


        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            AccountsBankBranch::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Branch successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Branch is Found Duplicate']);
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
        return AccountsBankBranch::findOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'bank_branch_names'=>'required|unique:accounts_bank_branches,bank_branch_names,'.$id.',id',
            'bank_branch_codes'=>'required|unique:accounts_bank_branches,bank_branch_codes,'.$id.',id',
            'bank_id'=>'required',
            'status'=>'required',
        ]);
        $data = AccountsBankBranch::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Branch successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Branch is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = AccountsBankBranch::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Branch successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
