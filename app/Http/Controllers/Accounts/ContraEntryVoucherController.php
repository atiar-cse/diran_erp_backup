<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsBankAccount;
use App\Model\Accounts\AccountsBankName;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsContraEntryVoucher;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\CheckBookLeafGenDetails;
use App\Model\HR\HrBranch;
use App\Model\LC\LcVoucherType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContraEntryVoucherController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('accounts_contra_entry_vouchers')
                ->leftJoin('accounts_chartof_accounts as dr_coa', 'accounts_contra_entry_vouchers.debit_account_id', '=', 'dr_coa.id')
                ->leftJoin('accounts_chartof_accounts as cr_coa', 'accounts_contra_entry_vouchers.credit_account_id', '=', 'cr_coa.id')
                ->leftJoin('users as ura', 'accounts_contra_entry_vouchers.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'accounts_contra_entry_vouchers.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'accounts_contra_entry_vouchers.approve_by', '=', 'urea.id')
                ->whereNull('accounts_contra_entry_vouchers.deleted_at')
                ->select(['accounts_contra_entry_vouchers.id AS id',
                    'accounts_contra_entry_vouchers.contra_entry_no',
                    'accounts_contra_entry_vouchers.contra_date',
                    'accounts_contra_entry_vouchers.payment_method',
                    'accounts_contra_entry_vouchers.debit_account_id',
                    'accounts_contra_entry_vouchers.credit_account_id',
                    'accounts_contra_entry_vouchers.amount',
                    'accounts_contra_entry_vouchers.created_by',
                    'accounts_contra_entry_vouchers.updated_by',
                    'accounts_contra_entry_vouchers.approve_by',
                    'accounts_contra_entry_vouchers.approve_status',
                    'dr_coa.chart_of_accounts_title as dr_chart_of_accounts_title',
                    'dr_coa.chart_of_account_code as dr_chart_of_account_code',
                    'cr_coa.chart_of_accounts_title as cr_chart_of_accounts_title',
                    'cr_coa.chart_of_account_code as cr_chart_of_account_code',
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

        return view('accounts.accounts_section.contra_entry_voucher', compact('chartofAccountBankWiseList', 'chartofAccountList', 'bankList', 'branchList'));
    }

    public function getContraTransactionNo()
    {
        $id        = AccountsContraEntryVoucher::withTrashed()->count();
        $currentId = $id + 1;

        //ContraEntry into CV
        return 'CV-' . date('Ym') . $currentId;
    }

    public function store(Request $request)
    {
        $inv_no_like = 'CV-';
        $rtn_val     = InvStore($request->contra_entry_no, $inv_no_like,
            'accounts_contra_entry_vouchers', 'contra_entry_no');

        $request->merge(['contra_entry_no' => $rtn_val]);

        $this->validate($request, [
            'contra_entry_no' => 'required|unique:accounts_contra_entry_vouchers,contra_entry_no',
            'contra_date' => 'required',
            'payment_method' => 'required',
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'amount' => 'required',
        ]);

        $input = $request->all();

//        $input['debit_remarks']  = $request->debit_remarks;
//        $input['credit_remarks'] = $request->credit_remarks;

        $input['contra_date'] = dateConvertFormtoDB($request->contra_date);
        $input['cheque_date'] = dateConvertFormtoDB($request->cheque_date);
        $input['created_by']  = Auth::user()->id;

        try {
            DB::beginTransaction();

            $contraData = AccountsContraEntryVoucher::create($input);

            if ($request->cheque_leaf) {
                $this->updateCheckLeafStatus('id', $request->cheque_leaf, 2); //2=In Draft
            }

            if ($input['approve_status'] == 1) {
                $this->journalDataEntry($request, $contraData->id);
                $this->debitAccountsBalanceIncDec($request);
                $this->creditAccountsBalanceIncDec($request);
                CheckBookLeafGenDetails::where('id', $contraData->cheque_leaf)->update(['use_status' => 1]);

                //////////////Approve By///////////////////////
                AccountsContraEntryVoucher::where('id', $contraData->id)->update([
                    'updated_by' => Auth::user()->id,
                    'approve_by' => Auth::user()->id,
                    'approve_at' => Carbon::now(),
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Contra Voucher # " . $request->contra_entry_no . ' Successfully Saved!']);

        } catch (Exception $e) {
//            print_r($e->getMessage());
//            die();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function updateCheckLeafStatus($column_name, $column_value, $status)
    {
        CheckBookLeafGenDetails::where($column_name, $column_value)->update(['use_status' => $status]);
    }

    private function journalDataEntry($request, $id)
    {

        $JournalData = [
            'transaction_reference_id' => $id,
            'type' => $request->type,
            'transaction_reference_no' => $request->contra_entry_no,
            'transaction_date' => dateConvertFormtoDB($request->contra_date),
            'transaction_type' => AccountsTransactionType::$contra,
            'transaction_type_name' => 'Contra Voucher',
            'cost_center_id' => $request->cost_center_id,
            'branch_id' => $request->branch_id,
            'narration' => $request->narration,
            'total_debit' => $request->amount,
            'total_credit' => $request->amount,
            'approve_status' => 1,
            'created_by' => Auth::user()->id,
        ];

        $result = AccountsJournalEntry::create($JournalData);

        $debitData = [
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->debit_account_id,
            'remarks' => $request->debit_remarks,
            'debit_amount' => $request->amount,
            'type_id' => $request->type_id,
            'type_desc' => $request->type_desc,
            'voucher_type_id' => $request->voucher_type_id,
            'voucher_ref_id' => $request->voucher_ref_id,
        ];

        AccountsJournalEntryDetails::insert($debitData);


        $creditData = [
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'remarks' => $request->credit_remarks,
            'credit_amount' => $request->amount,

            'type_id' => $request->type_id,
            'type_desc' => $request->type_desc,
            'voucher_type_id' => $request->voucher_type_id,
            'voucher_ref_id' => $request->voucher_ref_id,
        ];

        AccountsJournalEntryDetails::insert($creditData);

    }

    public function debitAccountsBalanceIncDec(Request $request)
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

    public function creditAccountsBalanceIncDec(Request $request)
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

    public function show(Request $request, $id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'unapprove') {

                //if (Auth::user()->email == 'software@iventurebd.com') {
                    $this->unApproveContraVoucher($id);
                    return response()->json(['status' => 'success', 'message' => 'Contra Voucher was successfully UnApproved!']);
                //}
            }
        }

        try {
            $showData = AccountsContraEntryVoucher::with('get_bank_account_no', 'get_check_leaf')->FindOrFail($id);

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
                ->where('transaction_reference_no', 'like', 'CV-%')
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

            if($showData->voucher_type_id != null)
            {
                $voucher_type = $showData->type == 'lc' ? $this->getVoucherTypeSingle($showData->voucher_type_id) : '';
                $voucher_ref  = $showData->type == 'lc' ? $this->getVoucherRefSingle($showData->voucher_type_id, $showData->voucher_ref_id) : '';
            }
            else
            {
                $voucher_type = '';
                $voucher_ref  = '';
            }




            $data = [
                'id' => $showData->id,
                'contra_entry_no' => $showData->contra_entry_no,
                'contra_date' => date('d/m/Y', strtotime($showData->contra_date)),

                'type' => $showData->type,

                'amount' => $showData->amount,
                'narration' => $showData->narration,

                'is_lc_type' => $showData->type == 'lc' ? true : false,
                'voucher_type_id' => $showData->voucher_type_id,
                'voucher_ref_id' => $showData->voucher_ref_id,

                'bank_name' => $bankName,
                'payment_method' => $showData->payment_method,
                'account_name' => $showData->account_name,
                'account_no' => $bankAccountNo,
                'cheque_date' => dateConvertDBtoForm($showData->cheque_date),
                'cheque_book' => $showData->cheque_book,
                'cheque_leaf' => $checkBookLeaf,
                'cheque_remarks' => $showData->cheque_remarks,

                'approve' => $showData->approve_status,
                'total_amount_word' => getCurrency($showData->amount),
                'get_details' => $this->showFormatData($showDetailsData, $voucher_type, $voucher_ref),
                'sub2_details' => $sb2_details,
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    private function showFormatData($data, $voucher_type, $voucher_ref)
    {
        $dataFormat = [];
        $count      = count($data);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'journal_entry_id' => $data[$i]['journal_entry_id'],
                'char_of_account_id' => $data[$i]['char_of_account_id'],
                'remarks' => $data[$i]['remarks'],
                'type_id' => $data[$i]['type_id'],
                'type_desc' => $data[$i]['type_desc'],
                'debit_amount' => $data[$i]['debit_amount'],
                'credit_amount' => $data[$i]['credit_amount'],
                'voucher_type_id' => $data[$i]['voucher_type_id'],
                'voucher_ref_id' => $data[$i]['voucher_ref_id'],
                'coa' => $data[$i]['coa'],
                'voucher_type' => $voucher_type,
                'voucher_ref' => $voucher_ref
            ];
        }
        return $dataFormat;
    }

    public function edit($id)
    {
        try {
            $editModeData = AccountsContraEntryVoucher::FindOrFail($id);

            $editModeDetailsData = CheckBookLeafGenDetails::where('id', $editModeData->cheque_leaf)->get();
            $editModeAccountNo   = AccountsBankAccount::where('id', $editModeData->account_no)->get();
            $data                = [
                'id' => $editModeData->id,
                'contra_entry_no' => $editModeData->contra_entry_no,
                'contra_date' => dateConvertDBtoForm($editModeData->contra_date),

                'type' => $editModeData->type,
                'type_id' => $editModeData->type_id,
                'type_desc' => $editModeData->type_desc,

                'payment_method' => $editModeData->payment_method,
                'amount' => $editModeData->amount,
                'narration' => $editModeData->narration,

                'is_lc_type' => $editModeData->type == 'lc' ? true : false,
                'voucher_type_id' => $editModeData->voucher_type_id,
                'voucher_ref_id' => $editModeData->voucher_ref_id,
                'voucher_ref_list' => $editModeData->voucher_type_id ? $this->getVoucherRef($editModeData->voucher_type_id) : '',

                'debit_account_id' => $editModeData->debit_account_id,
                'debit_remarks' => $editModeData->debit_remarks,
                'credit_account_id' => $editModeData->credit_account_id,
                'credit_remarks' => $editModeData->credit_remarks,

                'bank_id' => $editModeData->bank_id,
                'account_name' => $editModeData->account_name,
                'account_no' => $editModeData->account_no,
                'cheque_date' => dateConvertDBtoForm($editModeData->cheque_date),
                'cheque_leaf' => $editModeData->cheque_leaf,
                'cheque_remarks' => $editModeData->cheque_remarks,

                'type_data' => $this->getAccountTypeList($editModeData->type),
            ];

            $edit_data = [
                'data' => $data,
                'account_no' => $editModeAccountNo,
                'cheque_leaf' => $editModeDetailsData,
            ];
            return response()->json($edit_data);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function update(Request $request, $id)
    { //dd($request->all());
        $this->validate($request, [
            'contra_entry_no' => 'required|unique:accounts_contra_entry_vouchers,contra_entry_no,' . $id . ',id',
            'contra_date' => 'required',
            'payment_method' => 'required',
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
            'amount' => 'required',
        ]);

        $data  = AccountsContraEntryVoucher::findOrFail($id);
        $input = $request->all();

//        $input['debit_remarks']  = $request->debit_remarks;
//        $input['credit_remarks'] = '987';

        $input['contra_date'] = dateConvertFormtoDB($request->contra_date);
        $input['cheque_date'] = dateConvertFormtoDB($request->cheque_date);
        if ($input['approve_status'] != 1) {
            $input['updated_by'] = Auth::user()->id;
        }
        try {
            DB::beginTransaction();

            $data = AccountsContraEntryVoucher::findOrFail($id);

            if ($data->cheque_leaf || $request->cheque_leaf) {
                if ($data->cheque_leaf != $request->cheque_leaf) {
                    /* Check if it was changed. Then reverse status of the old one */
                    $this->updateCheckLeafStatus('id', $data->cheque_leaf, 0); //0=Available to use
                }
                $this->updateCheckLeafStatus('id', $request->cheque_leaf, 2); //2=In Draft
            }

//            $input                = $request->all();
            $input['contra_date'] = dateConvertFormtoDB($request->contra_date);
            $input['cheque_date'] = dateConvertFormtoDB($request->cheque_date);
            $input['updated_by']  = Auth::user()->id;

            $data->update($input);

            if ($input['approve_status'] == 1) {
                $this->journalDataEntry($request, $id);
                $this->debitAccountsBalanceIncDec($request);
                $this->creditAccountsBalanceIncDec($request);
                CheckBookLeafGenDetails::where('id', $data->cheque_leaf)->update(['use_status' => 1]);

                //////////////Approve By///////////////////////
                AccountsContraEntryVoucher::where('id', $id)->update([
                    'approve_by' => Auth::user()->id,
                    'approve_at' => Carbon::now(),
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Contra Voucher successfully Updated !']);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try {
            $data = AccountsContraEntryVoucher::FindOrFail($id);

            if ($data->cheque_leaf) {
                $this->updateCheckLeafStatus('id', $data->cheque_leaf, 0); //0=Available to use
            }

            $journalData = AccountsJournalEntry::where('transaction_reference_id', $data->id)
                ->where('transaction_type', AccountsTransactionType::$contra)
                ->first();
            if ($journalData) {
                AccountsJournalEntryDetails::where('journal_entry_id', $journalData->id)->delete();
                $journalData->delete();
            }

            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Contra Entry successfully Deleted !']);

        } catch (Exception $e) {
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function getAccountTypeList($type)
    {
        $type_list = getTypeList($type);
        return $type_list;
    }

    private function unApproveContraVoucher($id)
    {
        try {
            DB::beginTransaction();
            //Get Primary Table Data
            $data = AccountsContraEntryVoucher::where('approve_status', 1)->FindOrFail($id);

            //Get JournalEntry Table Data
            $journalData = AccountsJournalEntry::where('transaction_reference_id', $data->id)
                ->where('transaction_type', AccountsTransactionType::$contra)
                ->first();
            //REVERSE AMOUNT AND DELETE - JournalEntryDetails Table Data
            $allJournals = AccountsJournalEntryDetails::where('journal_entry_id', $journalData->id)->get();
            foreach ($allJournals as $row) {
                $amount = 0;
                $dr_cr  = Null;
                if ($row->debit_amount && ($row->debit_amount > 0)) {
                    $amount = $row->debit_amount;
                    $dr_cr  = 'dr';
                } elseif ($row->credit_amount && ($row->credit_amount > 0)) {
                    $amount = $row->credit_amount;
                    $dr_cr  = 'cr';
                }

                reverseDrCrAccountBalanceIncDec($row->char_of_account_id, $amount, $dr_cr);

                $row->delete();
            }

            $journalData->delete();

            CheckBookLeafGenDetails::where('id', $data->cheque_leaf)->update(['use_status' => 0]);

            //Update Status Primary Table Data
            $data->approve_status = 0;
            $data->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Contra Voucher was successfully UnApproved!']);
        } catch (Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }

    public function getAcctNumbList(Request $request)
    {
        return AccountsBankAccount::where('accounts_bank_id', $request->bank_id)->get();
    }

    public function getCheckLeafList(Request $request)
    {
        return CheckBookLeafGenDetails::where('account_number', $request->account_no)->where('use_status', 0)->get();
    }

    /* @ LC Vouchers Implementation */
    public function getVoucherRef($id) //Voucher Reference Data List - with ID,TransactionNo
    {
        $lc_voucher_type = LcVoucherType::Find($id);
        $column_name     = $lc_voucher_type->transaction_column; //Dynamic Column Name

        $results = DB::table($lc_voucher_type->menu_link_db_tbl)
            ->select('id', "$column_name")
            ->get();
        $data    = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->id,
                'transaction_no' => $row->$column_name //Dynamic Column Name
            ];
        }

        return $data;
    }

    public function getVoucherTypeSingle($id) //return voucher_type_name
    {
        $result = LcVoucherType::where('id', $id)->first();
        return $result ? $result->voucher_type_name : '';
    }

    public function getVoucherRefSingle($id, $select_id)
    {
        $lc_voucher_type = LcVoucherType::Find($id);
        $column_name     = $lc_voucher_type->transaction_column; //Dynamic Column Name

        $result = DB::table($lc_voucher_type->menu_link_db_tbl)
            ->where('id', $select_id)
            ->select('id', "$column_name")
            ->First();

        return $result ? $result->$column_name : '';
    }

}
