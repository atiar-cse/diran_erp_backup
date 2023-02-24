<?php

namespace App\Http\Controllers\Accounts;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Accounts\AccountsBankAccount;
use App\Model\Accounts\AccountsBankTransferDeposit;

use App\Model\Accounts\CheckBookLeaf;
use App\Model\Accounts\CheckBookLeafGenDetails;
use App\Model\Accounts\AccountsBankName;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsCostCenter;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;

use Illuminate\Support\Facades\Auth;
use App\Model\HR\HrBranch;
use DB;
use App\Lib\Enumerations\AccountsTransactionType;

class BankTransferDepoditController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            //return AccountsBankTransferDeposit::with('cost_center','debit_account','credit_account')->orderBy('id', 'desc')->paginate($request->perPage);

            $query = DB::table('accounts_bank_transfer_deposits')
                ->leftJoin('accounts_chartof_accounts', 'accounts_bank_transfer_deposits.debit_account_id', '=', 'accounts_chartof_accounts.id')
                ->leftJoin('users as ura', 'accounts_bank_transfer_deposits.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'accounts_bank_transfer_deposits.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'accounts_bank_transfer_deposits.approve_by', '=', 'urea.id')
                ->whereNull('accounts_bank_transfer_deposits.deleted_at')
                ->select(['accounts_bank_transfer_deposits.id AS id',
                    'accounts_bank_transfer_deposits.bank_transfer_no',
                    'accounts_bank_transfer_deposits.bank_transfer_date',
                    'accounts_bank_transfer_deposits.payment_method',
                    'accounts_bank_transfer_deposits.amount',
                    'accounts_bank_transfer_deposits.approve_status',
                    'accounts_bank_transfer_deposits.created_by',
                    'accounts_bank_transfer_deposits.updated_by',
                    'accounts_chartof_accounts.chart_of_accounts_title',
                    'accounts_chartof_accounts.chart_of_account_code',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        // $costCenterList     = AccountsCostCenter::where('status', '1')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();
        $bankList           = AccountsBankName::where('status', '1')->get();
        $branchList         = HrBranch::where('status', '1')->get();

        $chartofAccountBankWiseList = AccountsChartofAccounts::where('status', '1')
            ->where('chart_of_account_code', 'like', '3207%')
            ->get();

        return view('accounts.accounts_section.bank_transfer_deposite', compact('chartofAccountBankWiseList', 'chartofAccountList', 'bankList', 'branchList'));
    }

    public function getBankTransferNo()
    {
        $id        = AccountsBankTransferDeposit::withTrashed()->count();
        $currentId = $id + 1;
        return 'BV-' . date('Ym') . $currentId;
    }


    public function store(Request $request)
    {
        $inv_no_like = 'BV-';
        $rtn_val     = InvStore($request->bank_transfer_no, $inv_no_like,
            'accounts_bank_transfer_deposits', 'bank_transfer_no');

        $request->merge(['bank_transfer_no' => $rtn_val]);

        $this->validate($request, [
            'bank_transfer_no'   => 'required|unique:accounts_bank_transfer_deposits',
            'bank_transfer_date' => 'required',
            'payment_method'     => 'required',
            'debit_account_id'   => 'required',
            'credit_account_id'  => 'required',
            'amount'             => 'required',
        ]);

        $input                       = $request->all();
        $input['bank_transfer_date'] = dateConvertFormtoDB($request->bank_transfer_date);
        $input['cheque_date']        = dateConvertFormtoDB($request->cheque_date);
        $input['created_by']         = Auth::user()->id;

        try {
            DB::beginTransaction();

            $contraData = AccountsBankTransferDeposit::create($input);

            if ($request->cheque_leaf) {
                $this->updateCheckLeafStatus('id', $request->cheque_leaf, 2); //2=In Draft
            }

            if ($input['approve_status'] == 1) {

                $this->journalDataEntry($request, $contraData->id);
                $this->debitAccountsBalanceIncDec($request);
                $this->creditAccountsBalanceIncDec($request);
                CheckBookLeafGenDetails::where('id', $contraData->cheque_leaf)->update(['use_status' => 1]);

                if ($request->updated_by == Null) {
                    DB::table('accounts_bank_transfer_deposits')->where('id', $contraData->id)->update(array('updated_by' => Auth::user()->id));
                }

                AccountsBankTransferDeposit::where('id', $contraData->id)->update(array(
                    'approve_by' => Auth::user()->id,
                    'approve_at' => Carbon::now(),
                ));
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => "Bank Transfer/Deposit# " . $request->bank_transfer_no . ' Successfully Saved!']);
        } catch (\Exception $e) {

            print($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin === 'unapprove') {

                //if (Auth::user()->email == 'software@iventurebd.com') {
                $this->deleteCarefullyApprovedData($id);
                return response()->json(['status' => 'success', 'message' => 'Bank Transfer was successfully UnApproved!']);
                //}
            }
        }

        try {

            $showData = AccountsBankTransferDeposit::with('get_coa', 'get_check_leaf')->FindOrFail($id);

            if ($showData->get_coa != null) {
                $bankAccountNo = $showData->get_coa->chart_of_accounts_title . ' (' . $showData->get_coa->chart_of_account_code . ')';
            } else {
                $bankAccountNo = '';
            }

            if ($showData->cheque_leaf != null) {
                $checkBookLeaf = $showData->get_check_leaf->leaf_number;
            } else {
                $checkBookLeaf = '';
            }

            $jr_id = AccountsJournalEntry::where('transaction_reference_id', $id)
                ->where('transaction_reference_no', 'like', 'BV-%')
                ->first();

            $showDetailsData = AccountsJournalEntryDetails::with('coa')
                ->where('journal_entry_id', $jr_id->id)
                ->get();

            $sb2_details = AccountsJournalEntryDetails::with('coa')
                ->where('journal_entry_id', $jr_id->id)
                ->first();

            if ($showData->bank_id != null) {
                $bankName = AccountsBankName::where('id', $showData->bank_id)->first()->accounts_bank_names;
            } else {
                $bankName = '';
            }

            $data = [
                'id'                 => $showData->id,
                'bank_transfer_no'   => $showData->bank_transfer_no,
                'bank_transfer_date' => date('d/m/Y', strtotime($showData->bank_transfer_date)),

                'type'      => $showData->type,
                'amount'    => $showData->amount,
                'narration' => $showData->narration,

                'bank_name'      => $bankName,
                'payment_method' => $showData->payment_method,
                'account_name'   => $showData->account_name,
                'account_no'     => $bankAccountNo,
                'cheque_date'    => dateConvertDBtoForm($showData->cheque_date),
                'cheque_book'    => $showData->cheque_book,
                'cheque_leaf'    => $checkBookLeaf,
                'cheque_remarks' => $showData->cheque_remarks,

                'approve'           => $showData->approve_status,
                'total_amount_word' => getCurrency($showData->amount),
                'get_details'       => $showDetailsData,
                'sub2_details'      => $sb2_details,

            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {

            print($e->getMessage());
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function edit($id)
    {
        $editModeData        = AccountsBankTransferDeposit::FindOrFail($id);
        $editModeDetailsData = CheckBookLeafGenDetails::where('id', $editModeData->cheque_leaf)->get();
        $editModeAccountNo   = AccountsBankAccount::where('id', $editModeData->account_no)->get();
        $data                = [
            'id'                 => $editModeData->id,
            'bank_transfer_no'   => $editModeData->bank_transfer_no,
            'bank_transfer_date' => dateConvertDBtoForm($editModeData->bank_transfer_date),
            'type'               => $editModeData->type,
            'type_id'            => $editModeData->type_id,
            'type_desc'          => $editModeData->type_desc,
            'payment_method'     => $editModeData->payment_method,
            'amount'             => $editModeData->amount,
            'narration'          => $editModeData->narration,
            'debit_account_id'   => $editModeData->debit_account_id,
            'debit_remarks'      => $editModeData->debit_remarks,
            'credit_account_id'  => $editModeData->credit_account_id,
            'credit_remarks'     => $editModeData->credit_remarks,
            'bank_id'            => $editModeData->bank_id,
            'branch_id'          => $editModeData->branch_id,
            'account_name'       => $editModeData->account_name,
            'account_no'         => $editModeData->account_no,
            'cheque_date'        => dateConvertDBtoForm($editModeData->cheque_date),
            'cheque_leaf'        => $editModeData->cheque_leaf,
            'cheque_remarks'     => $editModeData->cheque_remarks,
            'type_data'          => $this->getAccountTypeList($editModeData->type),
        ];

        $edit_data = [
            'data'        => $data,
            'account_no'  => $editModeAccountNo,
            'cheque_leaf' => $editModeDetailsData,
        ];
        return response()->json($edit_data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'bank_transfer_no'   => 'required|unique:accounts_bank_transfer_deposits,bank_transfer_no,' . $id . ',id',
            'bank_transfer_date' => 'required',
            'payment_method'     => 'required',
            'debit_account_id'   => 'required',
            'credit_account_id'  => 'required',
            'amount'             => 'required',
        ]);

        $input                       = $request->all();
        $input['bank_transfer_date'] = dateConvertFormtoDB($request->bank_transfer_date);
        $input['cheque_date']        = dateConvertFormtoDB($request->cheque_date);

        if ($input['approve_status'] != 1) {
            $input['updated_by'] = Auth::user()->id;
        }

        try {
            DB::beginTransaction();

            $data = AccountsBankTransferDeposit::findOrFail($id);

            if ($data->cheque_leaf || $request->cheque_leaf) {
                if ($data->cheque_leaf != $request->cheque_leaf) {
                    /* Check if it was changed. Then reverse status of the old one */
                    $this->updateCheckLeafStatus('id', $data->cheque_leaf, 0); //0=Available to use
                }
                $this->updateCheckLeafStatus('id', $request->cheque_leaf, 2); //2=In Draft
            }

            $data->update($input);

            if ($input['approve_status'] == 1) {
                $this->journalDataEntry($request, $id);
                $this->debitAccountsBalanceIncDec($request);
                $this->creditAccountsBalanceIncDec($request);
                CheckBookLeafGenDetails::where('id', $data->cheque_leaf)->update(['use_status' => 1]);

                AccountsBankTransferDeposit::where('id', $data->id)->update(array(
                    'approve_by' => Auth::user()->id,
                    'approve_at' => Carbon::now(),
                ));
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Bank Transfer/Deposit successfully Updated !']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try {
            $data = AccountsBankTransferDeposit::FindOrFail($id);

            if ($data->cheque_leaf) {
                $this->updateCheckLeafStatus('id', $data->cheque_leaf, 0); //0=Available to use
            }

            $journalData = AccountsJournalEntry::where('transaction_reference_id', $data->id)
                ->where('transaction_type', AccountsTransactionType::$bankTransfer)
                ->first();
            if ($journalData) {
                AccountsJournalEntryDetails::where('journal_entry_id', $journalData->id)->delete();
                $journalData->delete();
            }

            $data->delete();

            return response()->json(['status' => 'success', 'message' => 'Bank Transfer/Deposit successfully Deleted !']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    private function debitAccountsBalanceIncDec(Request $request)
    {
        $debitAccountData = AccountsChartofAccounts::FindOrFail($request->debit_account_id);
        $debitCode        = mb_substr($debitAccountData->chart_of_account_code, 0, 1);
        $debitMainCode    = AccountsMainCode::where('main_code', $debitCode)->first();

        if ($debitMainCode->main_code_title == 'Assets') {

            AccountsChartofAccounts::where('id', $request->debit_account_id)
                ->increment('current_balance', $request->amount);

        } elseif ($debitMainCode->main_code_title == 'Expense') {

            AccountsChartofAccounts::where('id', $request->debit_account_id)
                ->increment('current_balance', $request->amount);

        } elseif ($debitMainCode->main_code_title == 'Income') {

            AccountsChartofAccounts::where('id', $request->debit_account_id)
                ->decrement('current_balance', $request->amount);

        } elseif ($debitMainCode->main_code_title == 'Liabilities') {

            AccountsChartofAccounts::where('id', $request->debit_account_id)
                ->decrement('current_balance', $request->amount);

        } elseif ($debitMainCode->main_code_title == 'Equity') { //Equity / Capital

            AccountsChartofAccounts::where('id', $request->debit_account_id)
                ->decrement('current_balance', $request->amount);

        }

    }

    private function creditAccountsBalanceIncDec(Request $request)
    {

        $creditAccountData = AccountsChartofAccounts::FindOrFail($request->credit_account_id);
        $creditCode        = mb_substr($creditAccountData->chart_of_account_code, 0, 1);
        $creditMainCode    = AccountsMainCode::where('main_code', $creditCode)->first();

        if ($creditMainCode->main_code_title == 'Assets') {

            AccountsChartofAccounts::where('id', $request->credit_account_id)
                ->decrement('current_balance', $request->amount);

        } elseif ($creditMainCode->main_code_title == 'Expense') {

            AccountsChartofAccounts::where('id', $request->credit_account_id)
                ->decrement('current_balance', $request->amount);

        } elseif ($creditMainCode->main_code_title == 'Income') {

            AccountsChartofAccounts::where('id', $request->credit_account_id)
                ->increment('current_balance', $request->amount);

        } elseif ($creditMainCode->main_code_title == 'Liabilities') {

            AccountsChartofAccounts::where('id', $request->credit_account_id)
                ->increment('current_balance', $request->amount);

        } elseif ($creditMainCode->main_code_title == 'Equity') { //Equity / Capital

            AccountsChartofAccounts::where('id', $request->credit_account_id)
                ->increment('current_balance', $request->amount);
        }
    }

    private function journalDataEntry($request, $id)
    {

        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => $request->bank_transfer_no,
            'transaction_date'         => dateConvertFormtoDB($request->bank_transfer_date),
            'transaction_type'         => AccountsTransactionType::$bankTransfer,
            'transaction_type_name'    => 'Bank Transfer/Deposit',
            'cost_center_id'           => '',
            'branch_id'                => $request->branch_id,
            'narration'                => $request->narration,
            'total_debit'              => $request->amount,
            'total_credit'             => $request->amount,
            'approve_status'           => 1,
            'created_by'               => Auth::user()->id,
        ];

        $result = AccountsJournalEntry::create($JournalData);

        $debitData = [
            'journal_entry_id'   => $result->id,
            'char_of_account_id' => $request->debit_account_id,
            'remarks'            => $request->debit_remarks,
            'debit_amount'       => $request->amount,
        ];

        AccountsJournalEntryDetails::insert($debitData);


        $creditData = [
            'journal_entry_id'   => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'remarks'            => $request->credit_remarks,
            'credit_amount'      => $request->amount,
        ];

        AccountsJournalEntryDetails::insert($creditData);

    }

    public function getAccountNumbList(Request $request)
    {
        return AccountsBankAccount::where('accounts_bank_id', $request->bank_id)->get();
    }

    public function getBankCheckLeafList(Request $request)
    {
        // return  CheckBookLeafGenDetails::where('account_number',$request->account_no)->where('use_status',0)->get();


        $IDs = CheckBookLeaf::where('chart_ac_id', $request->account_no)->where('approve_status', 1)->pluck('id');

        $leafs = CheckBookLeafGenDetails::whereIn('check_book_leaf_id', $IDs)->where('use_status', '0')->get();
        return $leafs;
    }

    public function getAccountTypeList($type)
    {
        $type_list = getTypeList($type);
        return $type_list;
    }

    private function updateCheckLeafStatus($column_name, $column_value, $status)
    {
        CheckBookLeafGenDetails::where($column_name, $column_value)->update(['use_status' => $status]);
    }

    private function deleteCarefullyApprovedData($id) {

        try{
            DB::beginTransaction();
            //Get Primary Table Data
            $data = AccountsBankTransferDeposit::FindOrFail($id);

            //Get JournalEntry Table Data
            $journalData = AccountsJournalEntry::where('transaction_reference_id',$data->id)
                ->where('transaction_type',AccountsTransactionType::$bankTransfer)
                ->first();
            //REVERSE AMOUNT AND DELETE - JournalEntryDetails Table Data
            $allJournals = AccountsJournalEntryDetails::where('journal_entry_id',$journalData->id)->get();
            foreach ($allJournals as $row) {
                $amount = 0;
                $dr_cr = Null;
                if ($row->debit_amount && ($row->debit_amount > 0) ){
                    $amount = $row->debit_amount;
                    $dr_cr = 'dr';
                } elseif($row->credit_amount && ($row->credit_amount > 0) ){
                    $amount= $row->credit_amount;
                    $dr_cr = 'cr';
                }

                reverseDrCrAccountBalanceIncDec($row->char_of_account_id, $amount,$dr_cr);

                $row->delete();
            }

            $journalData->delete();

            //Delete Primary Table Data
            $data->approve_status = 0;
            $data->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Bank Transfer Voucher was successfully UnApproved!']);
        }
        catch(\Exception $e){
            dd($e->getMessage());
        }
    }
}
