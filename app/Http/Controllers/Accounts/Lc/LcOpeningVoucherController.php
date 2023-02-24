<?php

namespace App\Http\Controllers\Accounts\Lc;

use App\Model\Accounts\AccountsBankName;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\LC\LcOpeningSection;
use App\Model\LC\LcProformaInvoiceEntry;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use DB;
use App\Lib\Enumerations\AccountsTransactionType;

class LcOpeningVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return LcOpeningSection::with('get_supplier','get_pi_no','get_opening_bank')
                ->where('approve_status',1)
                ->orderBy('id','desc')->paginate($request->perPage);
        }

        $piInvoiceLists = LcProformaInvoiceEntry::with('get_supplier','get_work_order')
            ->selectRaw('id,pi_no,supplier_id,work_order_id,total_amount_taka')
            ->where('status',1)->where('approve_status',1)->get();
        $bankLists = AccountsBankName::where('status',1)->selectRaw('id,accounts_bank_names')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();

        return view('accounts.lc_section.acc_lc_opening_section',
            compact('piInvoiceLists','bankLists','chartofAccountList'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        try {
            $editModeData = LcOpeningSection::with('get_supplier','get_pi_no','get_opening_bank','get_baneficiary_bank')
                ->FindOrFail($id);

            return response()->json(['status'=>'success','data'=>$editModeData]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcOpeningSection::FindOrFail($id);

            $open_id = AccountsJournalEntry::select('id','narration')->where('transaction_reference_id',$editModeData->id)
                ->where('transaction_type',AccountsTransactionType::$lcOpeningCharge)->first();

                $debit_account_id = 47;
                $credit_account_id = 16;
                $narration = '';

                if ($open_id) {
                    $char_of_account_ids = AccountsJournalEntryDetails::select('char_of_account_id')
                        ->where('journal_entry_id',$open_id->id)->get();
                    if(count($char_of_account_ids)>0){
                        $debit_account_id = $char_of_account_ids[0]->char_of_account_id;
                        $credit_account_id = $char_of_account_ids[1]->char_of_account_id ;
                        $narration = $open_id->narration;
                    }                        
                }

            $bank_id = AccountsJournalEntry::select('id','narration')->where('transaction_reference_id',$editModeData->id)
                ->where('transaction_type',AccountsTransactionType::$lcBankCharge)->first();
                    
                    $debit_account_idB = 47;
                    $credit_account_idB = 16;
                    $narration_idB = '';

                if ($bank_id) {
                    $char_of_account_ids_B = AccountsJournalEntryDetails::select('char_of_account_id')
                        ->where('journal_entry_id',$bank_id->id)->get();
                    if(count($char_of_account_ids_B)>0){
                        $debit_account_idB = $char_of_account_ids[0]->char_of_account_id;
                        $credit_account_idB = $char_of_account_ids[1]->char_of_account_id;
                        $narration_idB = $bank_id->narration;
                    }
                }

            $data = [
                'id'    => $editModeData->id,
                'lc_no' => $editModeData->lc_no,
                'supplier_id' => $editModeData->supplier_id,
                'supplier_info_display' =>  $editModeData->get_supplier ? $editModeData->get_supplier->purchase_supplier_name  : '',
                'proforma_invoice_id' => $editModeData->proforma_invoice_id,
                'lc_category' => $editModeData->lc_category,
                'lc_type' => $editModeData->lc_type,
                'lc_opening_date' => dateConvertDBtoForm($editModeData->lc_opening_date),
                'lc_opening_charges' => $editModeData->lc_opening_charges,
                'lc_opening_bank_id' => $editModeData->lc_opening_bank_id,
                'lc_bank_expenses' => $editModeData->lc_bank_expenses,
                'baneficiary_bank' => $editModeData->baneficiary_bank,
                'lc_latest_shipment_date' => dateConvertDBtoForm($editModeData->lc_latest_shipment_date),
                'lc_expire_date' => dateConvertDBtoForm($editModeData->lc_expire_date),
                'lc_total_value' => $editModeData->lc_total_value,

                'lc_status' => $editModeData->lc_status,
                'status' => $editModeData->status,

                'amount' => $editModeData->lc_opening_charges+$editModeData->lc_bank_expenses,

                'debit_account_id' => $debit_account_id,
                'credit_account_id' => $credit_account_id ,

                'debit_account_idB' => $debit_account_idB,
                'credit_account_idB' => $credit_account_idB,

                'narration' => $narration,
                'narrationB' => $narration_idB,
            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            //echo $e->getMessage();
            print_r($e->getMessage());
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'debit_account_idB'=>'required',
            'credit_account_idB'=>'required',
        ] );

        $input = $request->all();
        $input['lc_opening_date'] = dateConvertFormtoDB($request->lc_opening_date);
        $input['lc_latest_shipment_date'] = dateConvertFormtoDB($request->lc_latest_shipment_date);
        $input['lc_expire_date'] = dateConvertFormtoDB($request->lc_expire_date);
        $input['updated_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $editModeData = LcOpeningSection::FindOrFail($id);

            //Journal Entry Update with Details

            $journalIDS = AccountsJournalEntry::select('id')->where('transaction_reference_id',$id)->get()->toArray();
            $AccountsJournalEntry = AccountsJournalEntry::where('transaction_reference_id',$id);
            $AccountsJournalEntry->forceDelete();

            $AccountsJournalEntryDetails = AccountsJournalEntryDetails::whereIn('journal_entry_id',$journalIDS);
            $AccountsJournalEntryDetails->forceDelete();

            $this->journalDataEntry($request, $id);
            $this->journalDataEntryBank($request, $id);


            //

            if($approval == 1)
            {
                $this->approve($id);

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',$editModeData->lc_no)->First();
                $jr_data->approve_status = 1;
                $jr_data->save();

                //Opening Charge
                $data = array(
                    'debit_account_id' => $request->debit_account_id,
                    'credit_account_id' => $request->credit_account_id,
                    'amount' => $request->lc_opening_charges,
                );
                $object = (object)$data;
                $this->debitAccountsBalanceIncDec($object);
                $this->creditAccountsBalanceIncDec($object);

                //Bank Expense
                $data = array(
                    'debit_account_id' => $request->debit_account_idB,
                    'credit_account_id' => $request->credit_account_idB,
                    'amount' => $request->lc_bank_expenses,
                );
                $object = (object)$data;
                $this->debitAccountsBalanceIncDec($object);
                $this->creditAccountsBalanceIncDec($object);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Opening Section successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong!!!']);
        }
    }

    public function destroy($id)
    {
        //
    }

    private function journalDataEntry($request, $id){

        $approval = $request->approve;

        $jr_status = 0;
        if($approval ==1){
            $jr_status = 1;
        }

        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => $request->lc_no,
            'transaction_date' => dateConvertFormtoDB($request->lc_opening_date),
            'transaction_type' => AccountsTransactionType::$lcOpeningCharge,
            'transaction_type_name' => AccountsTransactionType::$lcOpeningChargeTitle,
            'cost_center_id' => '',
            'branch_id' => '',
            'narration' => $request->narration,
            'total_debit' => $request->lc_opening_charges,
            'total_credit' => $request->lc_opening_charges,
            'approve_status' => $jr_status,
            'created_by' => Auth::user()->id,
        ];
        $result = AccountsJournalEntry::create($JournalData);

        $debitData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->debit_account_id,
            'remarks' => $request->narration,
            'debit_amount' => $request->lc_opening_charges,
        ];
        AccountsJournalEntryDetails::insert($debitData);

        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'remarks' => $request->narration,
            'credit_amount' => $request->lc_opening_charges,
        ];
        AccountsJournalEntryDetails::insert($creditData);
    }

    private function journalDataEntryBank($request, $id){

        $approval = $request->approve;

        $jr_status = 0;
        if($approval ==1){
            $jr_status = 1;
        }

        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => $request->lc_no,
            'transaction_date' => dateConvertFormtoDB($request->lc_opening_date),
            'transaction_type' => AccountsTransactionType::$lcBankCharge,
            'transaction_type_name' => AccountsTransactionType::$lcBankChargeTitle,
            'cost_center_id' => '',
            'branch_id' => '',
            'narration' => $request->narrationB,
            'total_debit' => $request->lc_bank_expenses,
            'total_credit' => $request->lc_bank_expenses,
            'approve_status' => $jr_status,
            'created_by' => Auth::user()->id,
        ];

        $result = AccountsJournalEntry::create($JournalData);

        $debitData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->debit_account_idB,
            'remarks' => $request->narrationB,
            'debit_amount' => $request->lc_bank_expenses,
        ];
        AccountsJournalEntryDetails::insert($debitData);

        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_idB,
            'remarks' => $request->narrationB,
            'credit_amount' => $request->lc_bank_expenses,
        ];
        AccountsJournalEntryDetails::insert($creditData);

    }

    public function debitAccountsBalanceIncDec($request)
    {
        $debitAccountData = AccountsChartofAccounts::FindOrFail($request->debit_account_id);
        $debitCode = mb_substr($debitAccountData->chart_of_account_code, 0, 1);
        $debitMainCode = AccountsMainCode::where('main_code', $debitCode)->first();

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

    public function creditAccountsBalanceIncDec($request){

        $creditAccountData = AccountsChartofAccounts::FindOrFail($request->credit_account_id);
        $creditCode = mb_substr($creditAccountData->chart_of_account_code, 0, 1);
        $creditMainCode = AccountsMainCode::where('main_code', $creditCode)->first();

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

    function AddLcCharge(Request $request)
    {
        $old_data = LcOpeningSection::where('id',$request->id)->where('lc_no',$request->lc_no)->first();
        $new_val = $old_data->lc_opening_charges + $request->lc_opening_charges;
        $data = [
            'lc_opening_charges'    =>  $new_val,
        ];

        try{
            DB::beginTransaction();

            LcOpeningSection::where('id',$request->id)->where('lc_no',$request->lc_no)->update($data);
            $this->journalDataEntry($request, $request->id);

            if($old_data->account_status == 1){
                $data = array(
                    'debit_account_id' => $request->debit_account_id,
                    'credit_account_id' => $request->credit_account_id,
                    'amount' => $request->lc_opening_charges,
                );
                $object = (object)$data;
                $this->debitAccountsBalanceIncDec($object);
                $this->creditAccountsBalanceIncDec($object);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Addition Opening Charge successfully Added!']);
        }
        catch(\Exception $e){
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong']);
        }
    }

    private function approve($id)
    {
        $lc = LcOpeningSection::Find($id);
        if ($lc->account_status == 0) {

            $lc->account_status = 1;
            $lc->updated_by = Auth::user()->id;
            $lc->save();
        }
    }
}
