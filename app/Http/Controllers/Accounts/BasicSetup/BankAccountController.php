<?php

namespace App\Http\Controllers\Accounts\BasicSetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsBankName;
use App\Model\Accounts\AccountsBankBranch;
use App\Model\Accounts\AccountsBankAccount;
use App\Model\Accounts\AccountsChartofAccounts;

use Illuminate\Support\Facades\Auth;
use DB;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('accounts_bank_accounts')
                ->leftJoin('accounts_bank_names', 'accounts_bank_accounts.accounts_bank_id', '=', 'accounts_bank_names.id')
                ->leftJoin('accounts_bank_branches', 'accounts_bank_accounts.accounts_branch_id', '=', 'accounts_bank_branches.id')
                ->leftJoin('accounts_chartof_accounts', 'accounts_bank_accounts.chart_ac_id', '=', 'accounts_chartof_accounts.id')
                ->leftJoin('users as ura', 'accounts_bank_accounts.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_bank_accounts.updated_by','=','ured.id')
                ->whereNull('accounts_bank_accounts.deleted_at')
                ->select(['accounts_bank_accounts.id AS id',
                    'accounts_bank_accounts.accounts_bank_accounts_name',
                    'accounts_bank_accounts.accounts_bank_accounts_number',
                    'accounts_bank_accounts.accounts_bank_accounts_date',
                    'accounts_bank_accounts.accounts_bank_accounts_contact_person',
                    'accounts_bank_accounts.accounts_bank_accounts_contact_number',
                    'accounts_bank_accounts.created_by',
                    'accounts_bank_accounts.updated_by',
                    'accounts_bank_accounts.status',
                    'accounts_bank_names.accounts_bank_names',
                    'accounts_bank_branches.bank_branch_names',
                    'accounts_chartof_accounts.chart_of_accounts_title',
                    'accounts_chartof_accounts.chart_of_account_code',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',


                ]);

            return datatables()->of($query)->toJson();
        }

        $bankLists   = AccountsBankName::where('status', '1')->get();
        /*$chart_lists  = AccountsChartofAccounts::where('status','1')->where('chart_of_account_code','like','3207%')->get();*/
        $chart_lists  = AccountsChartofAccounts::where('status','1')->get();
        return view('accounts.accounts_setup.bank_account',compact('bankLists','chart_lists'));
    }

    public function getBankWiseBankBranch(Request $request)
    {
        return AccountsBankBranch::where('bank_id', $request->bank_id)->get();
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'accounts_bank_id'              => 'required',
            'accounts_branch_id'            => 'required',
            'accounts_bank_accounts_name'    => 'required',
            'accounts_bank_accounts_number' => 'required|unique:accounts_bank_accounts,accounts_bank_accounts_number',
            'chart_ac_id'                   => 'required',
        ]);

        if($request->accounts_bank_accounts_date!=''){
            $date = str_replace('/', '-', $request->accounts_bank_accounts_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val='';
        }


        $data = [
            'accounts_bank_id'                      => $request->accounts_bank_id,
            'accounts_branch_id'                    => $request->accounts_branch_id,
            'accounts_bank_accounts_name'           => $request->accounts_bank_accounts_name,
            'accounts_bank_accounts_number'         => $request->accounts_bank_accounts_number,
            'accounts_bank_accounts_date'           => $date_val,
            'accounts_bank_accounts_contact_person' => $request->accounts_bank_accounts_contact_person,
            'accounts_bank_accounts_contact_number' => $request->accounts_bank_accounts_contact_number,
            'chart_ac_id'                           => $request->chart_ac_id,
            'created_by'                            => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();
            $result = AccountsBankAccount::create($data);
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Bank Account successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $editModeData = AccountsBankAccount::FindOrFail($id);
        $branch=AccountsBankBranch::where('bank_id', $editModeData->accounts_bank_id)->get();
        $data = [
            'id'                                    => $editModeData->id,
            'accounts_bank_id'                      => $editModeData->accounts_bank_id,
            'accounts_branch_id'                    => $editModeData->accounts_branch_id,
            'accounts_bank_accounts_name'           => $editModeData->accounts_bank_accounts_name,
            'accounts_bank_accounts_number'         => $editModeData->accounts_bank_accounts_number,
            'accounts_bank_accounts_date'           => date('d/m/Y', strtotime($editModeData->accounts_bank_accounts_date)),
            'accounts_bank_accounts_contact_person' => $editModeData->accounts_bank_accounts_contact_person,
            'accounts_bank_accounts_contact_number' => $editModeData->accounts_bank_accounts_contact_number,
            'chart_ac_id'                           => $editModeData->chart_ac_id,
            'status'                                => $editModeData->status,
        ];
        $test_val = [
            'data' => $data,
            'branch' => $branch,
        ];
        return response()->json($test_val);

    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'accounts_bank_id' => 'required',
            'accounts_branch_id' => 'required',
            'accounts_bank_accounts_name' => 'required',
            'accounts_bank_accounts_number' => 'required|unique:accounts_bank_accounts,accounts_bank_accounts_number,'.$id.',id',
            'chart_ac_id' => 'required',
        ]);

        if($request->accounts_bank_accounts_date!=''){
            $date = str_replace('/', '-', $request->accounts_bank_accounts_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val='';
        }

        $data = [
            'accounts_bank_id'                      => $request->accounts_bank_id,
            'accounts_branch_id'                    => $request->accounts_branch_id,
            'accounts_bank_accounts_name'           => $request->accounts_bank_accounts_name,
            'accounts_bank_accounts_number'         => $request->accounts_bank_accounts_number,
            'accounts_bank_accounts_date'           => $date_val,
            'accounts_bank_accounts_contact_person' => $request->accounts_bank_accounts_contact_person,
            'accounts_bank_accounts_contact_number' => $request->accounts_bank_accounts_contact_number,
            'chart_ac_id'                           => $request->chart_ac_id,
            'updated_by'                            => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $editModeData = AccountsBankAccount::findOrFail($id);
            $editModeData->update($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Bank Account successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Branch is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = AccountsBankAccount ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Bank Account successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
