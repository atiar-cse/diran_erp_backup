<?php

namespace App\Http\Controllers\Accounts;

use App\Model\Accounts\AccountsBankAccount;
use App\Model\Accounts\AccountsBankReceiptDetailsVoucher;
use App\Model\Accounts\AccountsBankReceiptVoucher;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\Accounts\CheckBookLeaf;
use App\Model\Accounts\CheckBookLeafGenDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsCostCenter;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use Illuminate\Support\Facades\Auth;
use App\Model\HR\HrBranch;
use DB;
use App\Lib\Enumerations\AccountsTransactionType;

class BankReceiptVoucherController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('accounts_bank_receipt_vouchers')
                ->leftJoin('users as ura', 'accounts_bank_receipt_vouchers.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_bank_receipt_vouchers.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'accounts_bank_receipt_vouchers.approve_by','=','urea.id')
                ->whereNull('accounts_bank_receipt_vouchers.deleted_at')
                ->select(['accounts_bank_receipt_vouchers.id AS id',
                    'accounts_bank_receipt_vouchers.receipt_transaction_no',
                    'accounts_bank_receipt_vouchers.reciept_date',
                    'accounts_bank_receipt_vouchers.total_debit_amount',
                    'accounts_bank_receipt_vouchers.total_credit_amount',
                    'accounts_bank_receipt_vouchers.approve_status',
                    'accounts_bank_receipt_vouchers.created_by',
                    'accounts_bank_receipt_vouchers.updated_by',
                    'accounts_bank_receipt_vouchers.approve_by',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $costCenterList      = AccountsCostCenter::where('status', '1')->get();
        $subCode2List = AccountsSubCode2::where('status', '1')->get();
        $chartofAccountBankWiseList = AccountsChartofAccounts::where('status', '1')
                                          ->where('chart_of_account_code','like','3207%')
                                          ->orWhere('chart_of_account_code','like','4701%')
                                          ->orWhere('chart_of_account_code','like','4501%')
                                          ->get();
        $branchList = HrBranch::where('status', '1')->get();
        return view('accounts.accounts_section.bank_receipt_voucher',compact('costCenterList','subCode2List','chartofAccountBankWiseList','branchList'));
    }

    public function getBankReceiptNo(){
        $id = AccountsBankReceiptVoucher::withTrashed()->count();
        $currentId = $id+1;
        return 'BR-'.date('Ym').$currentId;
    }

    public function approve($id){
        $leafs = AccountsBankReceiptDetailsVoucher::where('bank_receipt_id',$id)->pluck('cheque_no');
        CheckBookLeafGenDetails::whereIn('leaf_number',$leafs)->update(['use_status'=> 1]);
    }

    public function store(Request $request)
    {
        $inv_no_like = 'BR-';
        $rtn_val =InvStore($request->receipt_transaction_no, $inv_no_like,
            'accounts_bank_receipt_vouchers','receipt_transaction_no');

        $request->merge(['receipt_transaction_no' => $rtn_val]) ;


        $this->validate($request, [
            'receipt_transaction_no'             => 'required|unique:accounts_bank_receipt_vouchers',
            'reciept_date'                       => 'required',
            'total_debit_amount'                 => 'required',
            'total_credit_amount'                => 'required',
            'debit_details.*.debit_account_id'   => 'required',
            'credit_details.*.credit_account_id' => 'required',
            'debit_details.*.debit_amount'       => 'required',
            'credit_details.*.credit_amount'     => 'required',
        ], [

            'debit_details.*.debit_account_id.required'   => 'Required field.',
            'credit_details.*.credit_account_id.required' => 'Required field.',
            'debit_details.*.debit_amount.required'       => 'Required field.',
            'credit_details.*.credit_amount.required'     => 'Required field.',
        ]);

        $data = [
            'receipt_transaction_no' => $request->receipt_transaction_no,
            'reciept_date'           => dateConvertFormtoDB($request->reciept_date),
            'type'                   => $request->type,
            'cost_center_id'         => $request->cost_center_id,
            'branch_id'              => $request->branch_id,
            'narration'              => $request->narration,
            'total_debit_amount'     => $request->total_debit_amount,
            'total_credit_amount'    => $request->total_credit_amount,
            'approve_status'         => $request->approve_status,
            'created_by'             => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result        = AccountsBankReceiptVoucher::create($data);
            $debitDetails  = $this->debitDataFormat($request, $result->id);
            $creditDetails = $this->creditDataFormat($request, $result->id);
            AccountsBankReceiptDetailsVoucher::insert($debitDetails);
            AccountsBankReceiptDetailsVoucher::insert($creditDetails);

            if($request->approve_status == 1)
            {
                $this->journalDataEntry($request, $result->id);
                $this->debitAccountBalanceIncDec($request);
                $this->creditAccountBalanceIncDec($request);
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('accounts_bank_receipt_vouchers')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
                DB::table('accounts_bank_receipt_vouchers')->where('id', $result->id)->update(array(
                    'approve_by' => Auth::user()->id,
                    'approve_at' => Carbon::now(),
                ));


            }


            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => "Bank Receipt Voucher # " . $request->receipt_transaction_no .' Successfully Saved!']);

        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Bank Receipt Voucher is Found Duplicate !']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    private function debitAccountBalanceIncDec(Request $request){

        $count = count($request->debit_details);
        for ($i = 0; $i < $count; $i++) {

            $chartofAccountData = AccountsChartofAccounts::FindOrFail($request->debit_details[$i]['debit_account_id']);
            $code = mb_substr($chartofAccountData->chart_of_account_code, 0, 1);
            $mainCode = AccountsMainCode::where('main_code',$code)->first();

            if($mainCode->main_code_title == 'Assets'){

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->increment('current_balance', $request->debit_details[$i]['debit_amount']);
            }
            elseif($mainCode->main_code_title == 'Expense'){

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->increment('current_balance', $request->debit_details[$i]['debit_amount']);

            }
            elseif($mainCode->main_code_title == 'Income'){

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->decrement('current_balance', $request->debit_details[$i]['debit_amount']);

            }
            elseif($mainCode->main_code_title == 'Liabilities'){

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->decrement('current_balance', $request->debit_details[$i]['debit_amount']);
            }
            elseif($mainCode->main_code_title == 'Equity'){ //Equity / Capital

                AccountsChartofAccounts::where('id', $request->debit_details[$i]['debit_account_id'])
                    ->decrement('current_balance', $request->debit_details[$i]['debit_amount']);
            }
        }
    }


    private function creditAccountBalanceIncDec(Request $request){

        $count = count($request->credit_details);
        for ($i = 0; $i < $count; $i++) {

            $chartofAccountData = AccountsChartofAccounts::FindOrFail($request->credit_details[$i]['credit_account_id']);
            $code = mb_substr($chartofAccountData->chart_of_account_code, 0, 1);
            $mainCode = AccountsMainCode::where('main_code',$code)->first();

            if($mainCode->main_code_title == 'Assets'){

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->increment('current_balance', $request->credit_details[$i]['credit_amount']);
            }
            elseif($mainCode->main_code_title == 'Expense'){

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->increment('current_balance', $request->credit_details[$i]['credit_amount']);

            }
            elseif($mainCode->main_code_title == 'Income'){

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->decrement('current_balance', $request->credit_details[$i]['credit_amount']);

            }
            elseif($mainCode->main_code_title == 'Liabilities'){

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->decrement('current_balance', $request->credit_details[$i]['credit_amount']);
            }
            elseif($mainCode->main_code_title == 'Equity'){ //Equity / Capital

                AccountsChartofAccounts::where('id', $request->credit_details[$i]['credit_account_id'])
                    ->decrement('current_balance', $request->credit_details[$i]['credit_amount']);
            }
        }
    }


    private function journalDataEntry($request, $id){

        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => $request->receipt_transaction_no,
            'transaction_date' => dateConvertFormtoDB($request->reciept_date),
            'transaction_type' => AccountsTransactionType::$bankReceipt,
            'transaction_type_name' => 'Bank Receipt Voucher',
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
        $count = count($request->debit_details);
        for ($i = 0; $i < $count; $i++) {
            $debitDataFormat[$i] = [
                'journal_entry_id' => $result->id,
                'char_of_account_id' => $request->debit_details[$i]['debit_account_id'],
                'remarks' => '',
                'debit_amount' => $request->debit_details[$i]['debit_amount'],
            ];
        }


        AccountsJournalEntryDetails::insert($debitDataFormat);

        $creditDataFormat = [];
        $count = count($request->credit_details);
        for ($i = 0; $i < $count; $i++) {
            $creditDataFormat[$i] = [
                'journal_entry_id'   => $result->id,
                'char_of_account_id' => $request->credit_details[$i]['credit_account_id'],
                'remarks' => '',
                'credit_amount'      => $request->credit_details[$i]['credit_amount'],
            ];
        }

        AccountsJournalEntryDetails::insert($creditDataFormat);



    }
    public function debitDataFormat($request, $id)
    {

        $dataFormat = [];
        $count = count($request->debit_details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'bank_receipt_id'  => $id,
                'debit_account_id' => $request->debit_details[$i]['debit_account_id'],
                'remarks'          => $request->debit_details[$i]['remarks'],
                'cheque_no'        => $request->debit_details[$i]['cheque_no'],
                'cheque_date'      => dateConvertFormtoDB($request->debit_details[$i]['cheque_date']),
                'debit_amount'     => $request->debit_details[$i]['debit_amount'],
            ];

            if ($request->debit_details[$i]['cheque_no']) {
                $this->updateCheckLeafStatus('leaf_number',$request->debit_details[$i]['cheque_no'],2); //2=In Draft
            }
        }
        return $dataFormat;
    }

    public function creditDataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->credit_details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'bank_receipt_id'   => $id,
                'sub_code2_id'      => $request->credit_details[$i]['sub_code2_id'],
                'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                'type_id'           => $request->credit_details[$i]['type_id'],
                'type_desc'         => $request->credit_details[$i]['type_desc'],
                'remarks'           => $request->credit_details[$i]['credit_remarks'],
                'credit_amount'     => $request->credit_details[$i]['credit_amount'],
            ];
        }
        return $dataFormat;
    }

    public function show(Request $request, $id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'unapprove') {

                //if (Auth::user()->email == 'software@iventurebd.com') {
                    $this->deleteCarefullyApprovedData($id);
                    return response()->json(['status' => 'success', 'message' => 'Bank Receipt Voucher was successfully UnApproved!']);
                //}
            }
        }

        try {
            $showData = AccountsBankReceiptVoucher::FindOrFail($id);
            $showDetailsData =AccountsBankReceiptDetailsVoucher::with('get_debit','get_credit','get_remarks')
                                                               ->where('bank_receipt_id',$id)
                                                               ->orderBy('id','ASC')
                                                               ->get();

            $debitData = AccountsBankReceiptDetailsVoucher::with('get_debit')
                                                           ->where('bank_receipt_id',$id)
                                                           ->where('debit_account_id','!=','NULL')
                                                           ->orderBy('id','desc')
                                                           ->first();

            $ref_code_no = AccountsBankReceiptDetailsVoucher::with('get_customer_code','get_supplier_code','get_lc_code','get_employee_id')
                                                           ->where('bank_receipt_id',$id)
                                                           ->where('credit_account_id','!=','NULL')
                                                           ->orderBy('id','desc')
                                                           ->first();

            $data = [
                'id' => $showData->id,
                'receipt_transaction_no'  => $showData->receipt_transaction_no,
                'reciept_date'            => date('d/m/Y', strtotime($showData->reciept_date)),
                'total_debit_amount'      => $showData->total_debit_amount,
                'type'                    => $showData->type,
                'total_credit_amount'     => $showData->total_credit_amount,
                'narration'               => $showData->narration,
                'approve'                 => $showData->approve_status,
                'total_amount_word'       => getCurrency($showData->total_credit_amount),
                'details'                 => $showDetailsData,
                'sub2_details'            => $debitData,
                'ref_code_details'       => $ref_code_no,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function edit($id)
    {
        try {
            $editModeData = AccountsBankReceiptVoucher::FindOrFail($id);
            $debitEditModeDetailsData = AccountsBankReceiptDetailsVoucher::where('bank_receipt_id',$id)
                                                       ->selectRaw('id,bank_receipt_id,sub_code2_id,debit_account_id,credit_account_id,remarks,cheque_no,type_id,type_desc,
                                                        DATE_FORMAT(cheque_date,"%d/%m/%Y") as  cheque_date,debit_amount,credit_amount')
                                                       ->where('debit_account_id','!=',null)
                                                       ->get();
            $debitDataFormat = [];
            foreach ($debitEditModeDetailsData as $row){

                $IDs = CheckBookLeaf::where('chart_ac_id',$row->debit_account_id)->where('approve_status',1)->pluck('id');
                $leafs = CheckBookLeafGenDetails::whereIn('check_book_leaf_id',$IDs)
                                ->where('use_status','0')
                                ->orWhere('leaf_number',$row->cheque_no)
                                ->get();


                $debitDataFormat[] = [
                    'id'               => $row->id,
                    'bank_receipt_id'  => $row->bank_receipt_id,
                    'sub_code2_id'     => $row->sub_code2_id,
                    'debit_account_id' => $row->debit_account_id,
                    'remarks'          => $row->remarks,
                    'cheque_no'        => $row->cheque_no,
                    'cheque_date'      => $row->cheque_date,
                    'type_id'          => $row->type_id,
                    'type_desc'        => $row->type_desc,
                    'debit_amount'     => $row->debit_amount,
                    'credit_amount'    => $row->credit_amount,
                    'check_leaf_lists' => $leafs,

                ];
            }

            $creditEditModeDetailsData = AccountsBankReceiptDetailsVoucher::where('bank_receipt_id',$id)
                                                        ->selectRaw('id,bank_receipt_id,sub_code2_id,debit_account_id,credit_account_id,remarks,cheque_no,type_id,type_desc,
                                                         cheque_date,debit_amount,credit_amount')
                                                        ->where('credit_account_id','!=',null)
                                                        ->get();


            $creditDataFormat = [];

            foreach ($creditEditModeDetailsData as $row){

                $coa = AccountsChartofAccounts::where('sub_code2_id',$row->sub_code2_id)->get();

                $creditDataFormat[] = [
                    'id'               => $row->id,
                    'bank_receipt_id'  => $row->bank_receipt_id,
                    'sub_code2_id'     => $row->sub_code2_id,
                    'credit_account_id'=> $row->credit_account_id,
                    'credit_remarks'   => $row->remarks,
                    'cheque_no'        => $row->cheque_no,
                    'cheque_date'      => $row->cheque_date,
                    'type_id'          => $row->type_id,
                    'type_desc'        => $row->type_desc,
                    'debit_amount'     => $row->debit_amount,
                    'credit_amount'    => $row->credit_amount,
                    'account_list'     => $coa,
                ];
            }

            $data = [
                'id'                     => $editModeData->id,
                'receipt_transaction_no' => $editModeData->receipt_transaction_no,
                'reciept_date'           => dateConvertDBtoForm($editModeData->reciept_date),
                'cost_center_id'         => $editModeData->cost_center_id,
                'type'                   => $editModeData->type,
                'branch_id'              => $editModeData->branch_id,
                'narration'              => $editModeData->narration,
                'total_debit_amount'     => $editModeData->total_debit_amount,
                'total_credit_amount'    => $editModeData->total_credit_amount,
                'deleteID' => [],
                'debit_details'          => $debitDataFormat,
                'credit_details'         => $creditDataFormat,
                'type_data'              => $this->getAccountTypeList($editModeData->type),
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            print_r($e->getMessage());
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'receipt_transaction_no' => 'required|unique:accounts_bank_receipt_vouchers,receipt_transaction_no,'.$id.',id',
            'reciept_date' => 'required',
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

            $editModeData = AccountsBankReceiptVoucher::FindOrFail($id);

            $editModeData ->receipt_transaction_no = $request->receipt_transaction_no;
            $editModeData ->reciept_date           = dateConvertFormtoDB($request->reciept_date);
            $editModeData ->type                   = $request->type;
            $editModeData ->narration              = $request->narration;
            $editModeData ->total_debit_amount     = $request->total_debit_amount;
            $editModeData ->total_credit_amount    = $request->total_credit_amount;

            if($request->approve_status!=1){
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            if (count($request->deleteID) > 0) {
                AccountsBankReceiptDetailsVoucher::whereIn('id', $request->deleteID)->delete();
            }

            $debitDataFormat = [];
            $count = count($request->debit_details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->debit_details[$i]['id']) && $request->debit_details[$i]['id'] !='') {
                    $updateData = [
                        'bank_receipt_id'  => $id,
                        'debit_account_id' => $request->debit_details[$i]['debit_account_id'],
                        'remarks'          => $request->debit_details[$i]['remarks'],
                        'cheque_no'        => $request->debit_details[$i]['cheque_no'],
                        'cheque_date'      => dateConvertFormtoDB($request->debit_details[$i]['cheque_date']),
                        'debit_amount'     => $request->debit_details[$i]['debit_amount'],
                    ];

                    $result = AccountsBankReceiptDetailsVoucher::where('id', $request->debit_details[$i]['id'])->First();
                    if ($result->cheque_no || $request->debit_details[$i]['cheque_no']) {
                        if ($result->cheque_no != $request->debit_details[$i]['cheque_no']) {
                            /* Check if it was changed. Then reverse status of the old one */
                            $this->updateCheckLeafStatus('leaf_number',$result->cheque_no,0); //0=Available to use
                        }
                        $this->updateCheckLeafStatus('leaf_number',$request->debit_details[$i]['cheque_no'],2); //2=In Draft
                    }
                    $result->update($updateData);
                } else {
                    $debitDataFormat[$i] =[
                        'bank_receipt_id'  => $id,
                        'debit_account_id' => $request->debit_details[$i]['debit_account_id'],
                        'remarks'          => $request->debit_details[$i]['remarks'],
                        'cheque_no'        => $request->debit_details[$i]['cheque_no'],
                        'cheque_date'      => dateConvertFormtoDB($request->debit_details[$i]['cheque_date']),
                        'debit_amount'     => $request->debit_details[$i]['debit_amount'],
                    ];

                    if ($request->debit_details[$i]['cheque_no']) {
                        $this->updateCheckLeafStatus('leaf_number',$request->debit_details[$i]['cheque_no'],2); //2=In Draft
                    }
                }
            }

            if(count($debitDataFormat) > 0){
                AccountsBankReceiptDetailsVoucher::insert($debitDataFormat);
            }


            $creditDataFormat = [];
            $count = count($request->credit_details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->credit_details[$i]['id']) && $request->credit_details[$i]['id'] !='') {
                    $updateData = [
                        'bank_receipt_id'   => $id,
                        'sub_code2_id'      => $request->credit_details[$i]['sub_code2_id'],
                        'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                        'type_id'           => $request->credit_details[$i]['type_id'],
                        'type_desc'         => $request->credit_details[$i]['type_desc'],
                        'credit_amount'     => $request->credit_details[$i]['credit_amount'],
                    ];
                    AccountsBankReceiptDetailsVoucher::where('id', $request->credit_details[$i]['id'])->update($updateData);
                } else {
                    $creditDataFormat[$i] =[
                        'bank_receipt_id'   => $id,
                        'sub_code2_id'      => $request->credit_details[$i]['sub_code2_id'],
                        'credit_account_id' => $request->credit_details[$i]['credit_account_id'],
                        'type_id'           => $request->credit_details[$i]['type_id'],
                        'type_desc'         => $request->credit_details[$i]['type_desc'],
                        'credit_amount'     => $request->credit_details[$i]['credit_amount'],
                    ];
                }
            }

            if(count($creditDataFormat) > 0){
                AccountsBankReceiptDetailsVoucher::insert($creditDataFormat);
            }

            if($request->approve_status == 1)
            {
                $this->journalDataEntry($request, $id);
                $this->debitAccountBalanceIncDec($request);
                $this->creditAccountBalanceIncDec($request);
                $this->approve($id);

                DB::table('accounts_bank_receipt_vouchers')->where('id', $id)->update(array(
                    'approve_status' => 1,
                    'approve_by'     => Auth::user()->id,
                    'approve_at'     => Carbon::now(),
                ));

            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = 1;//$e->errorInfo[1];
            $msg = $e->getMessage();
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Bank Receipt Voucher successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => $msg]);
        } else {
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }

    function getAccountTypeList($type) {

        try {

            $type_name_title = 'Type Name';
            $typeListData = [];

            if ($type == 'customer') {

                $type_name_title = 'Customer Name';
                $details = \App\Model\Sales\SalesCustomer::where('status',1)
                    ->select('id','sales_customer_name')
                    ->get();
                foreach ($details as $row) {
                    $typeListData[] = [
                        'id' => $row->id,
                        'type_name' => $row->sales_customer_name,
                    ];
                }
            } elseif ($type == 'supplier') {

                $type_name_title = 'Supplier Name';
                $details = \App\Model\Purchase\PurchaseSupplierEntry::where('status',1)
                    ->select('id','purchase_supplier_name')
                    ->get();
                foreach ($details as $row) {
                    $typeListData[] = [
                        'id' => $row->id,
                        'type_name' => $row->purchase_supplier_name,
                    ];
                }
            } elseif ($type == 'employee') {

                $type_name_title = 'Employee Name';
                $details = \App\Model\HR\HrManageEmployee::select('id','first_name','last_name')
                    ->get();
                foreach ($details as $row) {
                    $typeListData[] = [
                        'id' => $row->id,
                        'type_name' => "$row->first_name $row->last_name",
                    ];
                }
            } elseif ($type == 'lc') {

                $type_name_title = 'LC';
                $details = \App\Model\LC\LcOpeningSection::where('approve_status',1)
                    ->where('lc_closing_status',0)
                    ->select('id','lc_no')
                    ->get();
                foreach ($details as $row) {
                    $typeListData[] = [
                        'id' => $row->id,
                        'type_name' => "$row->lc_no",
                    ];
                }
            }

            return ['type_name_title' => $type_name_title, 'type_list' => $typeListData];
        }
        catch(\Exception $e){
            print_r($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $data = AccountsBankReceiptVoucher::FindOrFail($id);

            $results = AccountsBankReceiptDetailsVoucher::where('bank_receipt_id',$id)->get();
            foreach ($results as $item) {
                if ($item->cheque_no) {
                    $this->updateCheckLeafStatus('leaf_number',$item->cheque_no,0); //0=Available to use
                }
                $item->delete();
            }

            $journalData = AccountsJournalEntry::where('transaction_reference_id',$data->id)
                ->where('transaction_type',AccountsTransactionType::$bankReceipt)
                ->first();
                if ($journalData) {
                    AccountsJournalEntryDetails::where('journal_entry_id',$journalData->id)->delete();
                    $journalData->delete();
                }

            $data->delete();

            $bug = 0;

        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Bank Receipt Voucher successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function updateCheckLeafStatus($column_name,$column_value,$status){
        CheckBookLeafGenDetails::where($column_name,$column_value)->update(['use_status'=> $status]);
    }

    private function deleteCarefullyApprovedData($id) {

        try{
            DB::beginTransaction();
            //Get Primary Table Data
            $data = AccountsBankReceiptVoucher::FindOrFail($id);

            //DELETE - Primary Details Table Data
            AccountsBankReceiptDetailsVoucher::where('bank_receipt_id',$id)->delete();

            //Get JournalEntry Table Data
            $journalData = AccountsJournalEntry::where('transaction_reference_id',$data->id)
                ->where('transaction_type',AccountsTransactionType::$bankReceipt)
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
            return response()->json(['status' => 'success', 'message' => 'Bank Receipt Voucher was successfully UnApproved!']);
        }
        catch(\Exception $e){
            dd($e->getMessage());
        }
    }
}
