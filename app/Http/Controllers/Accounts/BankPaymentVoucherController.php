<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsBankPaymentDetailsVoucher;
use App\Model\Accounts\AccountsBankPaymentVoucher;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsCostCenter;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsProjectInfo;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\Accounts\CheckBookLeaf;
use App\Model\Accounts\CheckBookLeafGenDetails;
use App\Model\HR\HrBranch;
use App\Model\LC\LcVoucherType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankPaymentVoucherController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('accounts_bank_payment_vouchers')
                ->leftJoin('users as ura', 'accounts_bank_payment_vouchers.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'accounts_bank_payment_vouchers.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'accounts_bank_payment_vouchers.approve_by', '=', 'urea.id')
                ->whereNull('accounts_bank_payment_vouchers.deleted_at')
                ->select(['accounts_bank_payment_vouchers.id AS id',
                    'accounts_bank_payment_vouchers.payment_transaction_no',
                    'accounts_bank_payment_vouchers.payment_date',
                    'accounts_bank_payment_vouchers.total_debit_amount',
                    'accounts_bank_payment_vouchers.total_credit_amount',
                    'accounts_bank_payment_vouchers.approve_status',
                    'accounts_bank_payment_vouchers.created_by',
                    'accounts_bank_payment_vouchers.updated_by',
                    'accounts_bank_payment_vouchers.approve_by',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $costCenterList             = AccountsCostCenter::where('status', '1')->get();
        $chartofAccountBankWiseList = AccountsChartofAccounts::where('status', '1')
            ->where('chart_of_account_code', 'like', '3207%')
            ->orWhere('chart_of_account_code', 'like', '4501%')
            ->get();
        $branchList                 = HrBranch::where('status', '1')->get();
        $subCode2List               = AccountsSubCode2::where('status', '1')->get();

        return view('accounts.accounts_section.bank_payment_voucher', compact('costCenterList', 'chartofAccountBankWiseList', 'subCode2List', 'branchList'));
    }

    public function getBankPaymentNo()
    {
        $id        = AccountsBankPaymentVoucher::withTrashed()->count();
        $currentId = $id + 1;
        return 'BP-' . date('Ym') . $currentId;
    }

    public function getSubCode2WiseCoaLists($sub_code2_id)
    {
        $result = AccountsChartofAccounts::where('sub_code2_id', $sub_code2_id)->where('status', '1')->get();
        return $result;
    }

    public function getCheckLeafLists($credit_account_id)
    {
        $IDs   = CheckBookLeaf::where('chart_ac_id', $credit_account_id)->where('approve_status', 1)->pluck('id');
        $leafs = CheckBookLeafGenDetails::whereIn('check_book_leaf_id', $IDs)->where('use_status', '0')->get();
        return $leafs;
    }

    public function store(Request $request)
    {
        $inv_no_like = 'BP-';
        $rtn_val     = InvStore($request->payment_transaction_no, $inv_no_like,
            'accounts_bank_payment_vouchers', 'payment_transaction_no');

        $request->merge(['payment_transaction_no' => $rtn_val]);

        $this->validate($request, [
            'payment_transaction_no' => 'required|unique:accounts_bank_payment_vouchers',
            'payment_date' => 'required',
            'total_debit_amount' => 'required',
            'total_credit_amount' => 'required',
            'debit_details.*.debit_account_id' => 'required',
            'credit_details.*.credit_account_id' => 'required',
            'debit_details.*.debit_amount' => 'required',
            'credit_details.*.credit_amount' => 'required',
        ], [
            'debit_details.*.debit_account_id.required' => 'Required field.',
            'credit_details.*.credit_account_id.required' => 'Required field.',
            'debit_details.*.debit_amount.required' => 'Required field.',
            'credit_details.*.credit_amount.required' => 'Required field.',
        ]);

        $app  = $request->approve;
        $data = [
            'payment_transaction_no' => $request->payment_transaction_no,
            'payment_date' => dateConvertFormtoDB($request->payment_date),
            'type' => $request->type,
            'project_id' => $request->project_id,
            'cost_center_id' => $request->cost_center_id,
            'branch_id' => $request->branch_id,
            'narration' => $request->narration,
            'total_debit_amount' => $request->total_debit_amount,
            'total_credit_amount' => $request->total_credit_amount,
            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result        = AccountsBankPaymentVoucher::create($data);
            $debitDetails  = $this->debitDataFormat($request, $result->id);
            $creditDetails = $this->creditDataFormat($request, $result->id);
            AccountsBankPaymentDetailsVoucher::insert($debitDetails);
            AccountsBankPaymentDetailsVoucher::insert($creditDetails);

            if ($app == 1) {
                $this->journalDataEntry($request, $result->id);

                $this->debitAccountBalanceIncDec($request);
                $this->creditAccountBalanceIncDec($request);
                $this->approve($result->id);

                //////////////Approve By///////////////////////
                if ($request->updated_by == Null) {
                    AccountsBankPaymentVoucher::where('id', $result->id)->update(['updated_by' => Auth::user()->id,]);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Bank Payment Voucher # " . $request->payment_transaction_no . ' Successfully Saved!']);
        } catch (Exception $e) {
            print($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function debitDataFormat($request, $id)
    {
        $dataFormat = [];
        $count      = count($request->debit_details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'bank_payment_id' => $id,
                'sub_code2_id' => $request->debit_details[$i]['sub_code2_id'],
                'debit_account_id' => $request->debit_details[$i]['debit_account_id'],
                'type_id' => $request->debit_details[$i]['type_id'],
                'type_desc' => $request->debit_details[$i]['type_desc'],
                'debit_amount' => $request->debit_details[$i]['debit_amount'],
                'voucher_type_id' => $request->debit_details[$i]['voucher_type_id'],
                'voucher_ref_id' => $request->debit_details[$i]['voucher_ref_id'],
                'remarks' => $request->debit_details[$i]['remarks'],
            ];
        }
        return $dataFormat;
    }

    public function creditDataFormat($request, $id)
    {
        $dataFormat = [];
        $count      = count($request->credit_details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'bank_payment_id' => $id,
                'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                'remarks' => $request->credit_details[$i]['remarks'],
                //'check_book'        => $request->credit_details[$i]['check_book'],
                'check_leaf' => $request->credit_details[$i]['check_leaf'],
                'check_date' => dateConvertFormtoDB($request->credit_details[$i]['check_date']),
                'credit_amount' => $request->credit_details[$i]['credit_amount'],
            ];

            if ($request->credit_details[$i]['check_leaf']) {
                $this->updateCheckLeafStatus('leaf_number', $request->credit_details[$i]['check_leaf'], 2); //2=In Draft
            }
        }
        return $dataFormat;
    }

    private function updateCheckLeafStatus($column_name, $column_value, $status)
    {
        CheckBookLeafGenDetails::where($column_name, $column_value)->update(['use_status' => $status]);
    }

    private function journalDataEntry($request, $id)
    {
        $JournalData = [
            'transaction_reference_id' => $id,
            'type' => $request->project_id ? 'project' : $request->type,
            'transaction_reference_no' => $request->payment_transaction_no,
            'transaction_date' => dateConvertFormtoDB($request->payment_date),
            'transaction_type' => AccountsTransactionType::$bankPayment,
            'transaction_type_name' => 'Bank Payment Voucher',
            'cost_center_id' => $request->cost_center_id,
            'branch_id' => $request->branch_id,
            'narration' => $request->narration,
            'total_debit' => $request->total_debit_amount,
            'total_credit' => $request->total_credit_amount,
            'approve_status' => 1,
            'created_by' => Auth::user()->id,
        ];

        $result = AccountsJournalEntry::create($JournalData);

        $debitDataFormat = [];
        $count           = count($request->debit_details);
        $project_id      = $request->project_id;
        $project__name   = AccountsProjectInfo::find($request->project_id);

        for ($i = 0; $i < $count; $i++) {
            $debitDataFormat[$i] = [
                'journal_entry_id' => $result->id,
                'char_of_account_id' => $request->debit_details[$i]['debit_account_id'],
                'remarks' => $request->debit_details[$i]['remarks'],
                'debit_amount' => $request->debit_details[$i]['debit_amount'],

                'type_id' => $project_id ? $project_id : $request->debit_details[$i]['type_id'],
                'type_desc' => $project__name ? $project__name->project_name : $request->debit_details[$i]['type_desc'],
                'voucher_type_id' => $request->debit_details[$i]['voucher_type_id'],
                'voucher_ref_id' => $request->debit_details[$i]['voucher_ref_id'],
            ];
        }

        AccountsJournalEntryDetails::insert($debitDataFormat);

        $creditDataFormat = [];
        $count            = count($request->credit_details);
        for ($i = 0; $i < $count; $i++) {
            $creditDataFormat[$i] = [
                'journal_entry_id' => $result->id,
                'char_of_account_id' => $request->credit_details[$i]['credit_account_id'],
                'remarks' => '',
                'credit_amount' => $request->credit_details[$i]['credit_amount'],
            ];
        }

        AccountsJournalEntryDetails::insert($creditDataFormat);
    }

    private function debitAccountBalanceIncDec(Request $request)
    {

        $count = count($request->debit_details);
        for ($i = 0; $i < $count; $i++) {

            $chartofAccountData = AccountsChartofAccounts::FindOrFail($request->debit_details[$i]['debit_account_id']);
            $code               = mb_substr($chartofAccountData->chart_of_account_code, 0, 1);
            $mainCode           = AccountsMainCode::where('main_code', $code)->first();

            if ($mainCode->main_code_title == 'Assets') {

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->increment('current_balance', $request->debit_details[$i]['debit_amount']);
            } elseif ($mainCode->main_code_title == 'Expense') {

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->increment('current_balance', $request->debit_details[$i]['debit_amount']);
            } elseif ($mainCode->main_code_title == 'Income') {

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->decrement('current_balance', $request->debit_details[$i]['debit_amount']);
            } elseif ($mainCode->main_code_title == 'Liabilities') {

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->decrement('current_balance', $request->debit_details[$i]['debit_amount']);
            } elseif ($mainCode->main_code_title == 'Equity') { //Equity / Capital
                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->decrement('current_balance', $request->debit_details[$i]['debit_amount']);
            }
        }
    }

    private function creditAccountBalanceIncDec(Request $request)
    {

        $count = count($request->credit_details);
        for ($i = 0; $i < $count; $i++) {

            $chartofAccountData = AccountsChartofAccounts::FindOrFail($request->credit_details[$i]['credit_account_id']);
            $code               = mb_substr($chartofAccountData->chart_of_account_code, 0, 1);
            $mainCode           = AccountsMainCode::where('main_code', $code)->first();

            if ($mainCode->main_code_title == 'Assets') {

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->increment('current_balance', $request->credit_details[$i]['credit_amount']);
            } elseif ($mainCode->main_code_title == 'Expense') {

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->increment('current_balance', $request->credit_details[$i]['credit_amount']);
            } elseif ($mainCode->main_code_title == 'Income') {

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->decrement('current_balance', $request->credit_details[$i]['credit_amount']);
            } elseif ($mainCode->main_code_title == 'Liabilities') {

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->decrement('current_balance', $request->credit_details[$i]['credit_amount']);
            } elseif ($mainCode->main_code_title == 'Equity') { //Equity / Capital
                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->decrement('current_balance', $request->credit_details[$i]['credit_amount']);
            }
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'unapprove') {

               // if (Auth::user()->email == 'software@iventurebd.com') {
                    $this->unApproveBankPaymentVoucher($id);
                    return response()->json(['status' => 'success', 'message' => 'Bank Payment Voucher was successfully UnApproved!']);
                //}
            }
        }

        try {
            $showData        = AccountsBankPaymentVoucher::with('project_info')->FindOrFail($id);
            $showDetailsData = AccountsBankPaymentDetailsVoucher::with('get_debit', 'get_credit', 'get_remarks')
                ->where('bank_payment_id', $id)
                ->get();

            $creditData  = AccountsBankPaymentDetailsVoucher::with('get_credit')
                ->where('bank_payment_id', $id)
                ->where('credit_account_id', '!=', 'NULL')
                ->first();
            $ref_code_no = AccountsBankPaymentDetailsVoucher::with('get_customer_code', 'get_supplier_code', 'get_lc_code', 'get_employee_id')
                ->where('bank_payment_id', $id)
                ->where('debit_account_id', '!=', 'NULL')
                ->first();
            $data        = [
                'id' => $showData->id,
                'payment_transaction_no' => $showData->payment_transaction_no,
                'payment_date' => date('d/m/Y', strtotime($showData->payment_date)),
                'type' => $showData->type,
                'project' => $showData->project_info ? $showData->project_info->project_name : '',
                'total_debit_amount' => $showData->total_debit_amount,
                'total_credit_amount' => $showData->total_credit_amount,
                'narration' => $showData->narration,
                'approve' => $showData->approve_status,
                'total_amount_word' => getCurrency($showData->total_credit_amount),
                'details' => $this->showDataFormat($showDetailsData, $showData->type == 'lc'),
                'sbc2_details' => $creditData,
                'ref_code_details' => $ref_code_no,

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
        $count      = count($data);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'id' => $data[$i]->id,
                'bank_payment_id' => $data[$i]->bank_payment_id,
                'type_id' => $data[$i]->type_id,
                'type_desc' => $data[$i]->type_desc,
                'debit_account_id' => $data[$i]->debit_account_id,
                'sub_code2_id' => $data[$i]->sub_code2_id,
                'credit_account_id' => $data[$i]->credit_account_id,
                'remarks' => $data[$i]->remarks,
                'check_book' => $data[$i]->check_book,
                'check_leaf' => $data[$i]->check_leaf,
                'check_date' => $data[$i]->check_date,
                'debit_amount' => $data[$i]->debit_amount,
                'credit_amount' => $data[$i]->credit_amount,
                'voucher_type_id' => $data[$i]->voucher_type_id,
                'voucher_ref_id' => $data[$i]->voucher_ref_id,

                'voucher_type' => $is_lc_type ? $this->getVoucherTypeSingle($data[$i]->voucher_type_id) : '',
                'voucher_ref' => $is_lc_type && $data[$i]->voucher_ref_id ? $this->getVoucherRefSingle($data[$i]->voucher_type_id, $data[$i]->voucher_ref_id) : '',

                'get_debit' => $data[$i]->get_debit,
                'get_credit' => $data[$i]->get_credit,
                'get_remarks' => $data[$i]->get_remarks,
            ];
        }

        return $dataFormat;
    }

    public function edit($id)
    {
        try {
            $editModeData = AccountsBankPaymentVoucher::FindOrFail($id);

            $creditEditModeDetailsData = AccountsBankPaymentDetailsVoucher::where('bank_payment_id', $id)
                ->where('credit_account_id', '!=', null)
                ->selectRaw('id,bank_payment_id,debit_account_id,credit_account_id,sub_code2_id,remarks,check_leaf,type_id,type_desc,
                    DATE_FORMAT(check_date,"%d/%m/%Y") as  check_date,debit_amount,credit_amount')
                ->get();

            $creditDataFormat = [];
            foreach ($creditEditModeDetailsData as $row) {

                $IDs   = CheckBookLeaf::where('chart_ac_id', $row->credit_account_id)->where('approve_status', 1)->pluck('id');
                $leafs = CheckBookLeafGenDetails::whereIn('check_book_leaf_id', $IDs)
                    ->where('use_status', '0')
                    ->orWhere('leaf_number', $row->check_leaf)
                    ->get();

                $coa = AccountsChartofAccounts::where('sub_code2_id', $row->sub_code2_id)->get();

                $creditDataFormat[] = [
                    'id' => $row->id,
                    'sub_code2_id' => $row->sub_code2_id,
                    'debit_account_id' => $row->debit_account_id,
                    'credit_account_id' => $row->credit_account_id,
                    'remarks' => $row->remarks,
                    'type_id' => $row->type_id,
                    'type_desc' => $row->type_desc,
                    'check_date' => $row->check_date,
                    'check_leaf' => $row->check_leaf,
                    'debit_amount' => $row->debit_amount,
                    'credit_amount' => $row->credit_amount,
                    'check_leaf_lists' => $leafs,
                    'sb2_wise_coa_lists' => $coa,
                ];
            }

            $debitEditModeDetailsData = AccountsBankPaymentDetailsVoucher::where('bank_payment_id', $id)
                ->selectRaw('id,bank_payment_id,debit_account_id,credit_account_id,sub_code2_id,remarks,check_leaf,type_id,type_desc,
                                                                           debit_amount,credit_amount,check_date,voucher_type_id,voucher_ref_id')
                ->where('debit_account_id', '!=', null)
                ->get();
            $debitDataFormat          = [];

            foreach ($debitEditModeDetailsData as $row) {

                $coa = AccountsChartofAccounts::where('sub_code2_id', $row->sub_code2_id)->get();

                $debitDataFormat[] = [
                    'id' => $row->id,
                    'sub_code2_id' => $row->sub_code2_id,
                    'debit_account_id' => $row->debit_account_id,
                    'credit_account_id' => $row->credit_account_id,
                    'remarks' => $row->remarks,
                    'type_id' => $row->type_id,
                    'type_desc' => $row->type_desc,
                    'check_date' => $row->check_date,
                    'check_leaf' => $row->check_leaf,
                    'debit_amount' => $row->debit_amount,
                    'credit_amount' => $row->credit_amount,
                    'sb2_wise_coa_lists' => $coa,
                    'check_leaf_lists' => $leafs,

                    'voucher_ref_id' => $row->voucher_ref_id,
                    'voucher_type_id' => $row->voucher_type_id,
                    'voucher_ref_list' => $row->voucher_type_id ? $this->getVoucherRef($row->voucher_type_id) : ''
                ];
            }

            $data = [
                'id' => $editModeData->id,
                'payment_transaction_no' => $editModeData->payment_transaction_no,
                'payment_date' => dateConvertDBtoForm($editModeData->payment_date),
                'type' => $editModeData->type,
                'cost_center_id' => $editModeData->cost_center_id,
                'branch_id' => $editModeData->branch_id,
                'project_id' => $editModeData->project_id,
                'narration' => $editModeData->narration,
                'total_debit_amount' => $editModeData->total_debit_amount,
                'total_credit_amount' => $editModeData->total_credit_amount,
                'approve' => $editModeData->approve_status,
                'deleteID' => [],

                'debit_details' => $debitDataFormat,
                'credit_details' => $creditDataFormat,

                'type_data' => $this->getAccountTypeList($editModeData->type),

                // 'voucher_type_list' => $this->getVoucherType(),
                'is_lc_type' => $editModeData->type == 'lc' ? true : false,
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'payment_transaction_no' => 'required|unique:accounts_bank_payment_vouchers,payment_transaction_no,' . $id . ',id',
            'payment_date' => 'required',
            'total_debit_amount' => 'required',
            'total_credit_amount' => 'required',
            'debit_details.*.debit_account_id' => 'required',
            'credit_details.*.credit_account_id' => 'required',
            'debit_details.*.debit_amount' => 'required',
            'credit_details.*.credit_amount' => 'required',
        ], [
            'debit_details.*.debit_account_id.required' => 'Required field.',
            'credit_details.*.credit_account_id.required' => 'Required field.',
            'debit_details.*.debit_amount.required' => 'Required field.',
            'credit_details.*.credit_amount.required' => 'Required field.',
        ]);

        try {
            DB::beginTransaction();
            $app = $request->approve;

            $editModeData = AccountsBankPaymentVoucher::FindOrFail($id);

            $editModeData->payment_transaction_no = $request->payment_transaction_no;
            $editModeData->payment_date           = dateConvertFormtoDB($request->payment_date);
            $editModeData->type                   = $request->type;
            $editModeData->project_id             = $request->project_id;
            $editModeData->narration              = $request->narration;
            $editModeData->total_debit_amount     = $request->total_debit_amount;
            $editModeData->total_credit_amount    = $request->total_credit_amount;

            if ($app != 1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            if (count($request->deleteID) > 0) {
                AccountsBankPaymentDetailsVoucher::whereIn('id', $request->deleteID)->delete();
            }

            $debitDataFormat = [];
            $count           = count($request->debit_details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->debit_details[$i]['id']) && $request->debit_details[$i]['id'] != '') {
                    $updateData = [
                        'bank_payment_id' => $id,
                        'sub_code2_id' => $request->debit_details[$i]['sub_code2_id'],
                        'debit_account_id' => $request->debit_details[$i]['debit_account_id'],
                        'type_id' => $request->debit_details[$i]['type_id'],
                        'type_desc' => $request->debit_details[$i]['type_desc'],
                        'debit_amount' => $request->debit_details[$i]['debit_amount'],
                        'voucher_type_id' => $request->debit_details[$i]['voucher_type_id'],
                        'voucher_ref_id' => $request->debit_details[$i]['voucher_ref_id'],
                        'remarks' => $request->debit_details[$i]['remarks'],
                    ];
                    AccountsBankPaymentDetailsVoucher::where('id', $request->debit_details[$i]['id'])->update($updateData);
                } else {
                    $debitDataFormat[$i] = [
                        'bank_payment_id' => $id,
                        'sub_code2_id' => $request->debit_details[$i]['sub_code2_id'],
                        'debit_account_id' => $request->debit_details[$i]['debit_account_id'],
                        'type_id' => $request->debit_details[$i]['type_id'],
                        'type_desc' => $request->debit_details[$i]['type_desc'],
                        'debit_amount' => $request->debit_details[$i]['debit_amount'],
                        'voucher_type_id' => $request->debit_details[$i]['voucher_type_id'],
                        'voucher_ref_id' => $request->debit_details[$i]['voucher_ref_id'],
                        'remarks' => $request->debit_details[$i]['remarks'],
                    ];
                }
            }

            if (count($debitDataFormat) > 0) {
                AccountsBankPaymentDetailsVoucher::insert($debitDataFormat);
            }

            $creditDataFormat = [];
            $count            = count($request->credit_details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->credit_details[$i]['id']) && $request->credit_details[$i]['id'] != '') {
                    $updateData = [
                        'bank_payment_id' => $id,
                        'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                        'check_leaf' => $request->credit_details[$i]['check_leaf'],
                        'remarks' => $request->credit_details[$i]['remarks'],
                        'check_date' => dateConvertFormtoDB($request->credit_details[$i]['check_date']),
                        'credit_amount' => $request->credit_details[$i]['credit_amount'],
                    ];

                    $result = AccountsBankPaymentDetailsVoucher::where('id', $request->credit_details[$i]['id'])->First();
                    if ($result->check_leaf || $request->credit_details[$i]['check_leaf']) {
                        if ($result->check_leaf != $request->credit_details[$i]['check_leaf']) {
                            /* Check if it was changed. Then reverse status of the old one */
                            $this->updateCheckLeafStatus('leaf_number', $result->check_leaf, 0); //0=Available to use
                        }
                        $this->updateCheckLeafStatus('leaf_number', $request->credit_details[$i]['check_leaf'], 2); //2=In Draft
                    }
                    $result->update($updateData);
                } else {
                    $creditDataFormat[$i] = [
                        'bank_payment_id' => $id,
                        'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                        'check_leaf' => $request->credit_details[$i]['check_leaf'],
                        'remarks' => $request->credit_details[$i]['remarks'],
                        'check_date' => dateConvertFormtoDB($request->credit_details[$i]['check_date']),
                        'credit_amount' => $request->credit_details[$i]['credit_amount'],
                    ];

                    if ($request->credit_details[$i]['check_leaf']) {
                        $this->updateCheckLeafStatus('leaf_number', $request->credit_details[$i]['check_leaf'], 2); //2=In Draft
                    }
                }
            }

            if (count($creditDataFormat) > 0) {
                AccountsBankPaymentDetailsVoucher::insert($creditDataFormat);
            }

            if ($app == 1) {
                $this->journalDataEntry($request, $id);
                $this->debitAccountBalanceIncDec($request);
                $this->creditAccountBalanceIncDec($request);
                $this->approve($id);
            }

            /* $this->journalEditDataEntry($request, $id); */

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Bank Payment Voucher successfully Updated !']);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try {
            $data = AccountsBankPaymentVoucher::FindOrFail($id);

            $results = AccountsBankPaymentDetailsVoucher::where('bank_payment_id', $id)->get();
            foreach ($results as $item) {
                if ($item->check_leaf) {
                    $this->updateCheckLeafStatus('leaf_number', $item->check_leaf, 0); //0=Available to use
                }
                $item->delete();
            }
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Bank Payment Voucher successfully Deleted !']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $approval = DB::table('accounts_bank_payment_vouchers')->where('id', $id)->first()->approve_status;
        if ($approval == 0) {
            $creditData = AccountsBankPaymentDetailsVoucher::with('get_credit')
                ->where('bank_payment_id', $id)
                ->get();

            foreach ($creditData as $row) {
                CheckBookLeafGenDetails::where('leaf_number', $row->check_leaf)->update(['use_status' => 1]);
            }
            DB::table('accounts_bank_payment_vouchers')->where('id', $id)->update(array(
                'approve_status' => 1,
                'approve_by' => Auth::user()->id,
                'approve_at' => Carbon::now(),
            ));
        }
    }

    function getAccountTypeList($type)
    {
        $type_list = getTypeList($type);
        return $type_list;
    }

    private function unApproveBankPaymentVoucher($id)
    {
        try {
            DB::beginTransaction();
            //Get Primary Table Data
            $data = AccountsBankPaymentVoucher::where('approve_status', 1)->FindOrFail($id);

            //Get JournalEntry Table Data
            $journalData = AccountsJournalEntry::where('transaction_reference_id', $data->id)
                ->where('transaction_type', AccountsTransactionType::$bankPayment)
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

            //Update Status Primary Table Data
            $data->approve_status = 0;
            $data->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Bank Payment Voucher was successfully UnApproved!']);
        } catch (Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
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
            ->first();

        return $result ? $result->$column_name : '';
    }


}
