<?php

namespace App\Http\Controllers\Accounts;


use App\Http\Controllers\Controller;
use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsCashReceiptDetailsVoucher;
use App\Model\Accounts\AccountsCashReceiptVoucher;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsCostCenter;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\HR\HrBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashReceiptVoucherController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('accounts_cash_receipt_vouchers')
                ->leftJoin('users as ura', 'accounts_cash_receipt_vouchers.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'accounts_cash_receipt_vouchers.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'accounts_cash_receipt_vouchers.approve_by', '=', 'urea.id')
                ->whereNull('accounts_cash_receipt_vouchers.deleted_at')
                ->select(['accounts_cash_receipt_vouchers.id AS id',
                    'accounts_cash_receipt_vouchers.receipt_transaction_no',
                    'accounts_cash_receipt_vouchers.receipt_date',
                    'accounts_cash_receipt_vouchers.total_debit_amount',
                    'accounts_cash_receipt_vouchers.total_credit_amount',
                    'accounts_cash_receipt_vouchers.approve_status',
                    'accounts_cash_receipt_vouchers.created_by',
                    'accounts_cash_receipt_vouchers.updated_by',
                    'accounts_cash_receipt_vouchers.approve_by',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $costCenterList     = AccountsCostCenter::where('status', '1')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();
        $subCode2List       = AccountsSubCode2::where('status', '1')->get();
        $branchList         = HrBranch::where('status', '1')->get();
        return view('accounts.accounts_section.cash_receipt_voucher', compact('costCenterList', 'subCode2List', 'chartofAccountList', 'branchList'));
    }

    public function getCashReceiptNo()
    {
        $id        = AccountsCashReceiptVoucher::withTrashed()->count();
        $currentId = $id + 1;
        return 'CR-' . date('Ym') . $currentId;
    }

    public function approve($id)
    {
        $approval = DB::table('accounts_cash_receipt_vouchers')->where('id', $id)->first()->approve_status;
        if ($approval == 0) {
            DB::table('accounts_cash_receipt_vouchers')->where('id', $id)->update(array(
                'approve_status' => 1,
                'approve_by' => Auth::user()->id,
                'approve_at' => Carbon::now(),
            ));
        }
    }

    public function store(Request $request)
    {

        $inv_no_like = 'CR-';
        $rtn_val     = InvStore($request->receipt_transaction_no, $inv_no_like,
            'accounts_cash_receipt_vouchers', 'receipt_transaction_no');

        $request->merge(['receipt_transaction_no' => $rtn_val]);

        $this->validate($request, [
            'receipt_transaction_no' => 'required|unique:accounts_cash_receipt_vouchers',
            'receipt_date' => 'required',
            'total_debit_amount' => 'required',
            'total_credit_amount' => 'required',

            'debit_details.*.debit_account_id' => 'required',
            'debit_details.*.debit_amount' => 'required',

            'credit_details.*.credit_account_id' => 'required',
            'credit_details.*.credit_amount' => 'required',
        ], [
            'debit_details.*.debit_account_id.required' => 'Required field.',
            'debit_details.*.debit_amount.required' => 'Required field.',
            'credit_details.*.credit_account_id.required' => 'Required field.',
            'credit_details.*.credit_amount.required' => 'Required field.',
        ]);

        $app  = $request->approve;
        $data = [
            'receipt_transaction_no' => $request->receipt_transaction_no,
            'receipt_date' => dateConvertFormtoDB($request->receipt_date),
            'type' => $request->type,
            'cost_center_id' => $request->cost_center_id,
            'branch_id' => $request->branch_id,
            'narration' => $request->narration,
            'total_debit_amount' => $request->total_debit_amount,
            'total_credit_amount' => $request->total_credit_amount,
            'created_by' => Auth::user()->id,
        ];

        /* $result        = AccountsCashReceiptVoucher::create($data);
         $debitDetails  = $this->debitDataFormat($request, $result->id);
         $creditDetails = $this->creditDataFormat($request, $result->id);
         AccountsCashReceiptDetailsVoucher::insert($debitDetails);
         AccountsCashReceiptDetailsVoucher::insert($creditDetails);*/

        try {
            DB::beginTransaction();

            $result        = AccountsCashReceiptVoucher::create($data);
            $debitDetails  = $this->debitDataFormat($request, $result->id);
            $creditDetails = $this->creditDataFormat($request, $result->id);
            AccountsCashReceiptDetailsVoucher::insert($debitDetails);
            AccountsCashReceiptDetailsVoucher::insert($creditDetails);


            if ($app == 1) {
                $this->journalDataEntry($request, $result->id);

                $this->debitAccountBalanceIncDec($request);
                $this->creditAccountBalanceIncDec($request);
                $this->approve($result->id);

                if ($request->updated_by == Null) {
                    DB::table('accounts_cash_receipt_vouchers')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Cash Receipt Voucher # " . $request->receipt_transaction_no . ' Successfully Saved!']);

        } catch (\Exception $e) {
            DB::rollback();
//            print_r($e->getMessage());
//            die();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'unapprove') {

                //if (Auth::user()->email == 'software@iventurebd.com') {
                    $this->unApproveCashReceiptVoucher($id);
                    return response()->json(['status' => 'success', 'message' => 'Cash Receipt Voucher was successfully UnApproved!']);
                //}
            }
        }

        try {
            $showData        = AccountsCashReceiptVoucher::FindOrFail($id);
            $showDetailsData = AccountsCashReceiptDetailsVoucher::with('get_debit', 'get_credit', 'get_remarks')
                ->where('cash_receipt_id', $id)
                ->orderBy('id', 'DESC')
                ->get();

            $creditData = AccountsCashReceiptDetailsVoucher::with('get_credit', 'get_customer_code', 'get_supplier_code', 'get_lc_code', 'get_employee_id')
                ->where('cash_receipt_id', $id)
                ->where('credit_account_id', '!=', 'NULL')
                ->first();

            $data = [
                'id' => $showData->id,
                'receipt_transaction_no' => $showData->receipt_transaction_no,
                'receipt_date' => date('d/m/Y', strtotime($showData->receipt_date)),
                'type' => $showData->type,
                'total_debit_amount' => $showData->total_debit_amount,
                'total_credit_amount' => $showData->total_credit_amount,
                'narration' => $showData->narration,
                'approve' => $showData->approve_status,
                'total_amount_word' => getCurrency($showData->total_credit_amount),
                'details' => $showDetailsData,
                'credit_details' => $creditData,
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            $e->getMessage();
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData             = AccountsCashReceiptVoucher::FindOrFail($id);
            $debitEditModeDetailsData = AccountsCashReceiptDetailsVoucher::where('cash_receipt_id', $id)->where('debit_account_id', '!=', null)->get();

            $creditEditModeDetailsData = AccountsCashReceiptDetailsVoucher::where('cash_receipt_id', $id)
                ->select('id', 'cash_receipt_id', 'debit_account_id', 'credit_account_id', 'sub_code2_id', 'remarks', 'type_id', 'type_desc', 'money_receipt_no', 'debit_amount', 'credit_amount')
                ->where('credit_account_id', '!=', null)
                ->get();


            $creditDataFormat = [];

            foreach ($creditEditModeDetailsData as $row) {

                $coa = AccountsChartofAccounts::where('sub_code2_id', $row->sub_code2_id)->get();

                $creditDataFormat[] = [
                    'id' => $row->id,
                    'debit_account_id' => $row->debit_account_id,
                    'credit_account_id' => $row->credit_account_id,
                    'sub_code2_id' => $row->sub_code2_id,
                    'remarks' => $row->remarks,
                    'money_receipt_no' => $row->money_receipt_no,
                    'type_id' => $row->type_id,
                    'type_desc' => $row->type_desc,
                    'debit_amount' => $row->debit_amount,
                    'credit_amount' => $row->credit_amount,
                    'sb2_wise_coa_lists' => $coa,
                ];
            }

            $data = [
                'id' => $editModeData->id,
                'receipt_transaction_no' => $editModeData->receipt_transaction_no,
                'receipt_date' => dateConvertDBtoForm($editModeData->receipt_date),
                'type' => $editModeData->type,
                'cost_center_id' => $editModeData->cost_center_id,
                'branch_id' => $editModeData->branch_id,
                'narration' => $editModeData->narration,
                'total_debit_amount' => $editModeData->total_debit_amount,
                'total_credit_amount' => $editModeData->total_credit_amount,
                'approve' => $editModeData->approve_status,
                'deleteID' => [],
                'debit_details' => $debitEditModeDetailsData,
                'credit_details' => $creditDataFormat,

                'type_data' => $this->getAccountTypeList($editModeData->type),
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'receipt_transaction_no' => 'required|unique:accounts_cash_receipt_vouchers,receipt_transaction_no,' . $id . ',id',
            'receipt_date' => 'required',
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

            $editModeData = AccountsCashReceiptVoucher::FindOrFail($id);

            $editModeData->receipt_transaction_no = $request->receipt_transaction_no;
            $editModeData->receipt_date           = dateConvertFormtoDB($request->receipt_date);
            $editModeData->type                   = $request->type;
            $editModeData->narration              = $request->narration;
            $editModeData->total_debit_amount     = $request->total_debit_amount;
            $editModeData->total_credit_amount    = $request->total_credit_amount;

            if ($app != 1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            if (count($request->deleteID) > 0) {
                AccountsCashReceiptDetailsVoucher::whereIn('id', $request->deleteID)->delete();
            }

            $debitDataFormat = [];
            $count           = count($request->debit_details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->debit_details[$i]['id']) && $request->debit_details[$i]['id'] != '') {
                    $updateData = [
                        'cash_receipt_id' => $id,
                        'debit_account_id' => $request->debit_details[$i]['debit_account_id'],
                        'debit_amount' => $request->debit_details[$i]['debit_amount'],
                        'remarks' => $request->debit_details[$i]['remarks'],
                    ];
                    AccountsCashReceiptDetailsVoucher::where('id', $request->debit_details[$i]['id'])->update($updateData);
                } else {
                    $debitDataFormat[$i] = [
                        'cash_receipt_id' => $id,
                        'credit_account_id' => $request->debit_details[$i]['credit_account_id'],
                        'credit_amount' => $request->debit_details[$i]['credit_amount'],
                    ];
                }
            }

            if (count($debitDataFormat) > 0) {
                AccountsCashReceiptDetailsVoucher::insert($debitDataFormat);
            }

            $creditDataFormat = [];
            $count            = count($request->credit_details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->credit_details[$i]['id']) && $request->credit_details[$i]['id'] != '') {
                    $updateData = [
                        'cash_receipt_id' => $id,
                        'sub_code2_id' => $request->credit_details[$i]['sub_code2_id'],
                        'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                        'remarks' => $request->credit_details[$i]['remarks'],
                        'type_id' => $request->credit_details[$i]['type_id'],
                        'type_desc' => $request->credit_details[$i]['type_desc'],
                        'money_receipt_no' => $request->credit_details[$i]['money_receipt_no'],
                        'credit_amount' => $request->credit_details[$i]['credit_amount'],
                    ];
                    AccountsCashReceiptDetailsVoucher::where('id', $request->credit_details[$i]['id'])->update($updateData);
                } else {
                    $creditDataFormat[$i] = [
                        'cash_receipt_id' => $id,
                        'sub_code2_id' => $request->credit_details[$i]['sub_code2_id'],
                        'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                        'remarks' => $request->credit_details[$i]['remarks'],
                        'type_id' => $request->credit_details[$i]['type_id'],
                        'type_desc' => $request->credit_details[$i]['type_desc'],
                        'money_receipt_no' => $request->credit_details[$i]['money_receipt_no'],
                        'credit_amount' => $request->credit_details[$i]['credit_amount'],
                    ];
                }
            }

            if (count($creditDataFormat) > 0) {
                AccountsCashReceiptDetailsVoucher::insert($creditDataFormat);
            }

            if ($app == 1) {
                $this->journalDataEntry($request, $id);

                $this->debitAccountBalanceIncDec($request);
                $this->creditAccountBalanceIncDec($request);
                $this->approve($id);
            }

            /*$this->journalEditDataEntry($request, $id);*/

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Cash Receipt Voucher successfully Updated !']);
        } catch (\Exception $e) {
            DB::rollback();
//            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = AccountsCashReceiptVoucher::FindOrFail($id);
        try {
            AccountsCashReceiptDetailsVoucher::where('cash_receipt_id', $id)->delete();
            $data->delete();

            /*$journalData = AccountsJournalEntry::where('transaction_reference_id',$data->id)
                ->where('transaction_type',AccountsTransactionType::$cashReceipt)
                ->first();
            AccountsJournalEntryDetails::where('journal_entry_id',$journalData->id)->delete();
            AccountsJournalEntry::where('transaction_reference_id',$data->id)->where('transaction_type',AccountsTransactionType::$cashReceipt)->delete();*/

            return response()->json(['status' => 'success', 'message' => 'Cash Receipt Voucher successfully Deleted !']);
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Cash Receipt Voucher successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
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

    private function journalDataEntry($request, $id)
    {
        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => $request->receipt_transaction_no,
            'transaction_date' => dateConvertFormtoDB($request->receipt_date),
            'transaction_type' => AccountsTransactionType::$cashReceipt,
            'transaction_type_name' => 'Cash Receipt Voucher',
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
        for ($i = 0; $i < $count; $i++) {
            $debitDataFormat[$i] = [
                'journal_entry_id' => $result->id,
                'char_of_account_id' => $request->debit_details[$i]['debit_account_id'],
                // 'remarks' => $request->debit_details[$i]['remarks'],
                'debit_amount' => $request->debit_details[$i]['debit_amount'],
            ];
        }

        AccountsJournalEntryDetails::insert($debitDataFormat);

        $creditDataFormat = [];
        $count            = count($request->credit_details);
        for ($i = 0; $i < $count; $i++) {
            $creditDataFormat[$i] = [
                'journal_entry_id' => $result->id,
                'char_of_account_id' => $request->credit_details[$i]['credit_account_id'],
                'remarks' => $request->credit_details[$i]['remarks'],
                'credit_amount' => $request->credit_details[$i]['credit_amount'],
            ];
        }

        AccountsJournalEntryDetails::insert($creditDataFormat);
    }

    public function debitDataFormat($request, $id)
    {
        $dataFormat = [];
        $count      = count($request->debit_details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'cash_receipt_id' => $id,
                'debit_account_id' => $request->debit_details[$i]['debit_account_id'],
                'debit_amount' => $request->debit_details[$i]['debit_amount'],
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
                'cash_receipt_id' => $id,
                'sub_code2_id' => $request->credit_details[$i]['sub_code2_id'],
                'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                'remarks' => $request->credit_details[$i]['remarks'],
                'type_id' => $request->credit_details[$i]['type_id'],
                'type_desc' => $request->credit_details[$i]['type_desc'],
                'money_receipt_no' => $request->credit_details[$i]['money_receipt_no'],
                'credit_amount' => $request->credit_details[$i]['credit_amount'],
            ];
        }
        return $dataFormat;
    }

    function getAccountTypeList($type)
    {
        try {

            $type_name_title = 'Type Name';
            $typeListData    = [];

            if ($type == 'customer') {

                $type_name_title = 'Customer Name';
                $details         = \App\Model\Sales\SalesCustomer::where('status', 1)
                    ->select('id', 'sales_customer_name')
                    ->get();
                foreach ($details as $row) {
                    $typeListData[] = [
                        'id' => $row->id,
                        'type_name' => $row->sales_customer_name,
                    ];
                }
            } elseif ($type == 'supplier') {

                $type_name_title = 'Supplier Name';
                $details         = \App\Model\Purchase\PurchaseSupplierEntry::where('status', 1)
                    ->select('id', 'purchase_supplier_name')
                    ->get();
                foreach ($details as $row) {
                    $typeListData[] = [
                        'id' => $row->id,
                        'type_name' => $row->purchase_supplier_name,
                    ];
                }
            } elseif ($type == 'employee') {

                $type_name_title = 'Employee Name';
                $details         = \App\Model\HR\HrManageEmployee::select('id', 'first_name', 'last_name')
                    ->get();
                foreach ($details as $row) {
                    $typeListData[] = [
                        'id' => $row->id,
                        'type_name' => "$row->first_name $row->last_name",
                    ];
                }
            } elseif ($type == 'lc') {

                $type_name_title = 'LC';
                $details         = \App\Model\LC\LcOpeningSection::where('approve_status', 1)
                    ->where('lc_closing_status', 0)
                    ->select('id', 'lc_no')
                    ->get();
                foreach ($details as $row) {
                    $typeListData[] = [
                        'id' => $row->id,
                        'type_name' => "$row->lc_no",
                    ];
                }
            }

            return ['type_name_title' => $type_name_title, 'type_list' => $typeListData];
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    private function unApproveCashReceiptVoucher($id)
    {
        try {
            DB::beginTransaction();
            //Get Primary Table Data
            $data = AccountsCashReceiptVoucher::where('approve_status', 1)->FindOrFail($id);

            //Get JournalEntry Table Data
            $journalData = AccountsJournalEntry::where('transaction_reference_id', $data->id)
                ->where('transaction_type', AccountsTransactionType::$cashReceipt)
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
            return response()->json(['status' => 'success', 'message' => 'Cash Receipt Voucher was successfully UnApproved!']);
        } catch (\Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }
}
