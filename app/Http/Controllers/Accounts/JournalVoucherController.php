<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsCostCenter;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\HR\HrBranch;
use App\Model\LC\LcVoucherType;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalVoucherController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $query = DB::table('accounts_journal_entries')
                ->leftJoin('users as ura', 'accounts_journal_entries.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'accounts_journal_entries.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'accounts_journal_entries.approve_by', '=', 'urea.id')
                ->where('accounts_journal_entries.transaction_type', 1)
                ->whereNull('accounts_journal_entries.deleted_at')
                ->orderBy('accounts_journal_entries.transaction_type', 'ASC')
                ->select(['accounts_journal_entries.id AS id',
                    'accounts_journal_entries.transaction_type',
                    'accounts_journal_entries.transaction_reference_no',
                    'accounts_journal_entries.transaction_date',
                    'accounts_journal_entries.total_debit',
                    'accounts_journal_entries.total_credit',
                    'accounts_journal_entries.created_by',
                    'accounts_journal_entries.updated_by',
                    'accounts_journal_entries.approve_by',
                    'accounts_journal_entries.approve_status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $costCenterList = AccountsCostCenter::where('status', '1')->get();
        $branchList = HrBranch::where('status', '1')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();
        return view('accounts.accounts_section.journal_voucher', compact('costCenterList', 'chartofAccountList', 'branchList'));
    }

    public function getJournalTransactionNo()
    {
        $id = AccountsJournalEntry::count();
        $currentId = $id + 1;
        //Journal will change in JV
        return 'JV-' . date('Ym') . $currentId;
    }

    public function store(Request $request)
    {
        $inv_no_like = 'JV-';
        $rtn_val = InvStore($request->transaction_reference_no, $inv_no_like,
            'accounts_journal_entries', 'transaction_reference_no');

        $request->merge(['transaction_reference_no' => $rtn_val]);

        $this->validate($request, [
            'transaction_reference_no' => 'required|unique:accounts_journal_entries',
            'transaction_date' => 'required',
            'total_debit' => 'required',
            'total_credit' => 'required',
            'details.*.char_of_account_id' => 'required',
        ], [
            'details.*.char_of_account_id.required' => 'Required field.',
        ]);

        $data = [
            'transaction_reference_no' => $request->transaction_reference_no,
            'transaction_date' => dateConvertFormtoDB($request->transaction_date),
            'type' => $request->type,
            'transaction_type' => AccountsTransactionType::$journal,
            'transaction_type_name' => 'Journal Voucher',
            'narration' => $request->narration,
            'total_debit' => $request->total_debit,
            'total_credit' => $request->total_credit,
            'approve_status' => $request->approve,
            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = AccountsJournalEntry::create($data);
            $details = $this->dataFormat($request, $result->id);
            AccountsJournalEntryDetails::insert($details);

            if ($request->approve == 1) {
                $this->currentBalanceIncrementDecrement($request);

                if ($request->updated_by == Null) {
                    DB::table('accounts_journal_entries')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }

                DB::table('accounts_journal_entries')->where('id', $result->id)->update(array(
                    'approve_status' => 1,
                    'approve_by' => Auth::user()->id,
                    'approve_at' => Carbon::now(),
                ));
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Journal Voucher  # " . $request->transaction_reference_no . ' Successfully Saved!']);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->details[$i]['char_of_account_id'],

                'remarks' => $request->details[$i]['remarks'],
                'type_id' => $request->details[$i]['type_id'],
                'type_desc' => $request->details[$i]['type_desc'],

                'debit_amount' => $request->details[$i]['debit_amount'],
                'credit_amount' => $request->details[$i]['credit_amount'],

                'voucher_type_id' => $request->details[$i]['voucher_type_id'],
                'voucher_ref_id' => $request->details[$i]['voucher_ref_id'],
            ];
        }

        return $dataFormat;
    }

    public function currentBalanceIncrementDecrement(Request $request)
    {
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {

            $chartofAccountData = AccountsChartofAccounts::FindOrFail($request->details[$i]['char_of_account_id']);
            $code = mb_substr($chartofAccountData->chart_of_account_code, 0, 1);
            $mainCode = AccountsMainCode::where('main_code', $code)->first();

            if ($mainCode->main_code_title == 'Assets') {

                if (isset($request->details[$i]['debit_amount']) != 0) {
                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->increment('current_balance', $request->details[$i]['debit_amount']);

                } else {
                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $request->details[$i]['credit_amount']);
                }

            } elseif ($mainCode->main_code_title == 'Expense') {

                if (isset($request->details[$i]['debit_amount']) != 0) {
                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->increment('current_balance', $request->details[$i]['debit_amount']);

                } else {

                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $request->details[$i]['credit_amount']);
                }

            } elseif ($mainCode->main_code_title == 'Income') {

                if (isset($request->details[$i]['debit_amount']) != 0) {
                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $request->details[$i]['debit_amount']);

                } else {

                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->increment('current_balance', $request->details[$i]['credit_amount']);
                }
            } elseif ($mainCode->main_code_title == 'Liabilities') {

                if (isset($request->details[$i]['debit_amount']) != 0) {
                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $request->details[$i]['debit_amount']);

                } else {

                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->increment('current_balance', $request->details[$i]['credit_amount']);
                }
            } elseif ($mainCode->main_code_title == 'Equity') { //Equity / Capital

                if (isset($request->details[$i]['debit_amount']) != 0) {
                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $request->details[$i]['debit_amount']);
                } else {

                    AccountsChartofAccounts::where('id', $request->details[$i]['char_of_account_id'])
                        ->increment('current_balance', $request->details[$i]['credit_amount']);
                }
            }
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'unapprove') {

                //if (Auth::user()->email == 'software@iventurebd.com') {
                    $this->unApproveJournalVoucher($id);
                    return response()->json(['status' => 'success', 'message' => 'Journal Voucher was successfully UnApproved!']);
                //}
            }
        }

        try {
            $showData = AccountsJournalEntry::FindOrFail($id);
            $showDetailsData = AccountsJournalEntryDetails::with('get_jrnl_entry', 'get_credit', 'get_supplier_code', 'get_customer_code', 'get_lc_code', 'get_employee_id')->where('journal_entry_id', $id)->get();

            $sub2Data = AccountsJournalEntryDetails::with('coa')->where('journal_entry_id', $id)->first();
            $typeCodeDebitDetails = AccountsJournalEntryDetails::with('get_supplier_code', 'get_customer_code', 'get_lc_code', 'get_employee_id')->where('journal_entry_id', $id)->where('type_id', '!=', 'NULL')->first();
            $typeCodeCreditDetails = AccountsJournalEntryDetails::with('get_supplier_code', 'get_customer_code', 'get_lc_code', 'get_employee_id')->where('journal_entry_id', $id)->where('type_id', '!=', 'NULL')->get();

            $data = [
                'id' => $showData->id,
                'transaction_reference_no' => $showData->transaction_reference_no,
                'transaction_date' => date('d/m/Y', strtotime($showData->transaction_date)),
                'narration' => $showData->narration,

                'type' => $showData->type,
                'total_debit' => $showData->total_debit,
                'total_credit' => $showData->total_credit,
                'approve' => $showData->approve_status,
                'total_amount_word' => getCurrency($showData->total_credit),
                'details' => $this->showDataFormat($showDetailsData, $showData->type),
                'sub2_details' => $sub2Data,
                'dr_code_type' => $typeCodeDebitDetails,
                'cr_code_type' => $typeCodeCreditDetails,

                'is_lc_type' => $showData->type == 'lc' ? true : false,
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    private function showDataFormat($data, $is_lc_type)
    {
        $dataFormat = [];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'id' => $data[$i]->id,
                'journal_entry_id' => $data[$i]->journal_entry_id,
                'char_of_account_id' => $data[$i]->char_of_account_id,
                'remarks' => $data[$i]->remarks,
                'type_id' => $data[$i]->type_id,
                'type_desc' => $data[$i]->type_desc,
                'debit_amount' => $data[$i]->debit_amount,
                'credit_amount' => $data[$i]->credit_amount,
                'voucher_type_id' => $data[$i]->voucher_type_id,
                'voucher_ref_id' => $data[$i]->voucher_ref_id,
                'voucher_type' => $is_lc_type ? $this->getVoucherTypeSingle($data[$i]->voucher_type_id) : '',
                'voucher_ref' => $is_lc_type && $data[$i]->voucher_ref_id ? $this->getVoucherRefSingle($data[$i]->voucher_type_id, $data[$i]->voucher_ref_id) : '',  //Check every row - if voucher_ref_id is not null

                'get_jrnl_entry' => $data[$i]->get_jrnl_entry,
                'get_credit' => $data[$i]->get_credit,
                'get_supplier_code' => $data[$i]->get_supplier_code,
                'get_customer_code' => $data[$i]->get_customer_code,
                'get_lc_code' => $data[$i]->get_lc_code,
                'get_employee_id' => $data[$i]->get_employee_id
            ];
        }

        return $dataFormat;
    }

    public function edit($id)
    {
        try {
            $editModeData = AccountsJournalEntry::FindOrFail($id);
            $editModeDetailsData = AccountsJournalEntryDetails::where('journal_entry_id', $id)->get();
            $data = [
                'id' => $editModeData->id,
                'transaction_reference_no' => $editModeData->transaction_reference_no,
                'transaction_date' => dateConvertDBtoForm($editModeData->transaction_date),
                'type' => $editModeData->type,
                'transaction_type' => $editModeData->transaction_type,
                'cost_center_id' => $editModeData->cost_center_id,
                'branch_id' => $editModeData->branch_id,
                'narration' => $editModeData->narration,
                'total_debit' => $editModeData->total_debit,
                'total_credit' => $editModeData->total_credit,
                'deleteID' => [],
                'details' => $this->editDataFormat($editModeDetailsData, $editModeData->type == 'lc'),

                'type_data' => $this->getAccountTypeList($editModeData->type),

                'voucher_type_list' => $this->getVoucherType(),
                'is_lc_type' => $editModeData->type == 'lc' ? true : false,
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function editDataFormat($data, $is_lc_type)
    {
        $dataFormat = [];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'id' => $data[$i]->id,
                'journal_entry_id' => $data[$i]->journal_entry_id,
                'char_of_account_id' => $data[$i]->char_of_account_id,
                'remarks' => $data[$i]->remarks,
                'type_id' => $data[$i]->type_id,
                'type_desc' => $data[$i]->type_desc,
                'debit_amount' => $data[$i]->debit_amount,
                'credit_amount' => $data[$i]->credit_amount,
                'voucher_type_id' => $data[$i]->voucher_type_id,
                'voucher_ref_id' => $data[$i]->voucher_ref_id,
                'voucher_ref_list' => $is_lc_type && $data[$i]->voucher_type_id ? $this->getVoucherRef($data[$i]->voucher_type_id) : '', //Check every row - if voucher_type_id is not null
            ];
        }

        return $dataFormat;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'transaction_reference_no' => 'required',
            'transaction_date' => 'required',
            'total_debit' => 'required',
            'total_credit' => 'required',
            'details.*.char_of_account_id' => 'required',
        ], [
            'details.*.char_of_account_id.required' => 'Required field.',
        ]);

        try {
            DB::beginTransaction();

            $editModeData = AccountsJournalEntry::FindOrFail($id);

            $editModeData->transaction_reference_no = $request->transaction_reference_no;
            $editModeData->transaction_date = dateConvertFormtoDB($request->transaction_date);
            $editModeData->type = $request->type;
            $editModeData->transaction_type = AccountsTransactionType::$journal;
            $editModeData->transaction_type_name = 'Journal Voucher';
            $editModeData->narration = $request->narration;
            $editModeData->total_debit = $request->total_debit;
            $editModeData->total_credit = $request->total_credit;
            $editModeData->approve_status = $request->approve;

            if ($request->approve != 1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            /* Insert update and delete rm requisition details  */

            if (count($request->deleteID) > 0) {
                AccountsJournalEntryDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] != '') {
                    $updateData = [
                        'journal_entry_id' => $id,
                        'char_of_account_id' => $request->details[$i]['char_of_account_id'],
                        'remarks' => $request->details[$i]['remarks'],
                        'type_id' => $request->details[$i]['type_id'],
                        'type_desc' => $request->details[$i]['type_desc'],
                        'debit_amount' => $request->details[$i]['debit_amount'],
                        'credit_amount' => $request->details[$i]['credit_amount'],
                        'voucher_type_id' => $request->details[$i]['voucher_type_id'],
                        'voucher_ref_id' => $request->details[$i]['voucher_ref_id'],
                    ];
                    AccountsJournalEntryDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] = [
                        'journal_entry_id' => $id,
                        'char_of_account_id' => $request->details[$i]['char_of_account_id'],
                        'remarks' => $request->details[$i]['remarks'],
                        'type_id' => $request->details[$i]['type_id'],
                        'type_desc' => $request->details[$i]['type_desc'],
                        'debit_amount' => $request->details[$i]['debit_amount'],
                        'credit_amount' => $request->details[$i]['credit_amount'],
                        'voucher_type_id' => $request->details[$i]['voucher_type_id'],
                        'voucher_ref_id' => $request->details[$i]['voucher_ref_id'],
                    ];
                }
            }

            if (count($dataFormat) > 0) {
                AccountsJournalEntryDetails::insert($dataFormat);
            }

            if ($request->approve == 1) {
                $this->currentBalanceIncrementDecrement($request);
                DB::table('accounts_journal_entries')->where('id', $id)->update([
                    'approve_status' => 1,
                    'approve_by' => Auth::user()->id,
                    'approve_at' => Carbon::now(),
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Journal Entry successfully updated!']);
        } catch (Exception $e) {
            //print($e->getMessage());die();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try {
            AccountsJournalEntryDetails::where('journal_entry_id', $id)->delete();
            AccountsJournalEntry::FindOrFail($id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Journal Entry successfully Deleted !']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function getAccountTypeList($type)
    {
        $type_list = getTypeList($type);
        return $type_list;
    }

    private function unApproveJournalVoucher($id)
    {
        try {
            DB::beginTransaction();
            //Get Primary Table Data
            //Get JournalEntry Table Data
            $journalData = AccountsJournalEntry::where('approve_status', 1)
                ->where('transaction_type', AccountsTransactionType::$journal)
                ->FindOrFail($id);

            //REVERSE AMOUNT - JournalEntryDetails Table Data
            $allJournals = AccountsJournalEntryDetails::where('journal_entry_id', $journalData->id)->get();
            foreach ($allJournals as $row) {
                $amount = 0;
                $dr_cr = Null;
                if ($row->debit_amount && ($row->debit_amount > 0)) {
                    $amount = $row->debit_amount;
                    $dr_cr = 'dr';
                } elseif ($row->credit_amount && ($row->credit_amount > 0)) {
                    $amount = $row->credit_amount;
                    $dr_cr = 'cr';
                }

                reverseDrCrAccountBalanceIncDec($row->char_of_account_id, $amount, $dr_cr);
            }

            //Update Status Primary Table Data
            $journalData->approve_status = 0;
            $journalData->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Journal Voucher was successfully UnApproved!']);
        } catch (Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }

    /* @ LC Vouchers Implementation */
    public function getVoucherType() //Voucher Type List
    {
        return LcVoucherType::query()->select('id','voucher_type_name')->get();
    }

    public function getVoucherRef($id) //Voucher Reference Data List - with ID,TransactionNo
    {
        $lc_voucher_type = LcVoucherType::Find($id);
        $column_name = $lc_voucher_type->transaction_column; //Dynamic Column Name

        $results = DB::table($lc_voucher_type->menu_link_db_tbl)
                        ->select('id',"$column_name")
                        ->get();
        $data = [];
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
        $column_name = $lc_voucher_type->transaction_column; //Dynamic Column Name

        $result = DB::table($lc_voucher_type->menu_link_db_tbl)
                        ->where('id', $select_id)
                        ->select('id',"$column_name")
                        ->First();

        return $result ? $result->$column_name : '';
    }
}
