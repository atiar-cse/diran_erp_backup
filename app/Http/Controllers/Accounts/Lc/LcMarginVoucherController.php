<?php

namespace App\Http\Controllers\Accounts\Lc;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\LC\LcCfValueMarginEntry;
use App\Model\LC\LcOpeningSection;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class LcMarginVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_cf_value_margin_entries')
                ->leftJoin('purchase_supplier_entries', 'lc_cf_value_margin_entries.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('lc_opening_sections', 'lc_cf_value_margin_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('accounts_bank_names', 'lc_cf_value_margin_entries.bank_id', '=', 'accounts_bank_names.id')
                ->leftJoin('users as ura', 'lc_cf_value_margin_entries.created_by','=','ura.id')
                ->leftJoin('users as uredac', 'lc_cf_value_margin_entries.acc_updated_by','=','uredac.id')
                ->leftJoin('users as ureac', 'lc_cf_value_margin_entries.acc_approve_by','=','ureac.id')
                ->whereNull('lc_cf_value_margin_entries.deleted_at')
                ->select(['lc_cf_value_margin_entries.id AS id',
                    'lc_cf_value_margin_entries.margin_entry_date',
                    'lc_cf_value_margin_entries.lc_value',
                    'lc_cf_value_margin_entries.margin_percentage',
                    'lc_cf_value_margin_entries.margin_value',
                    'lc_cf_value_margin_entries.narration',
                    'lc_cf_value_margin_entries.created_by',
                    'lc_cf_value_margin_entries.acc_updated_by',
                    'lc_cf_value_margin_entries.acc_approve_by',
                    'lc_cf_value_margin_entries.account_status',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'lc_opening_sections.lc_no',
                    'accounts_bank_names.accounts_bank_names',
                    'ura.user_name as ad_name',
                    'uredac.user_name as ac_ed_name',
                    'ureac.user_name as ac_ap_name'
                ]);

            return datatables()->of($query)->toJson();


        }

        $lcLists = LcOpeningSection::with('get_supplier','get_opening_bank')
            ->where('status',1)
            ->where('approve_status',1)
            ->where('lc_closing_status',0)
            ->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();

        return view('accounts.lc_section.acc_lc_margin_entry',compact('lcLists','chartofAccountList'));
    }


    public function show($id)
    {
        try {
            $editModeData = LcCfValueMarginEntry::with('get_lc_no','get_supplier','get_bank')->FindOrFail($id);

            return response()->json(['status'=>'success','data'=>$editModeData]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcCfValueMarginEntry::FindOrFail($id);

            $jr_data = AccountsJournalEntry::where('transaction_reference_id',$id)
                        ->where('transaction_type',AccountsTransactionType::$lcMarginValue)->First();
            $debit_account_id = '';
            $credit_account_id = '';
            $narration = '';
            if($jr_data){
                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                $narration = $jr_data->narration;
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $debit_account_id = $row->char_of_account_id;
                    }
                    if ($row->credit_amount > 0) {
                        $credit_account_id = $row->char_of_account_id;
                    }
                }
            }else{
                $debit_account_id = 47;
                $credit_account_id = 53;
                $narration = $editModeData->narration;
            }

            $data = [
                'id'    => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'supplier_id' => $editModeData->supplier_id,
                'margin_entry_date' => dateConvertDBtoForm($editModeData->margin_entry_date),
                'bank_id' => $editModeData->bank_id,
                'lc_value' => $editModeData->lc_value,
                'margin_percentage' => $editModeData->margin_percentage,
                'margin_value' => $editModeData->margin_value,
                'narration' => $narration,
                'status' => $editModeData->status,
                'supplier_info_display' => '', //Populated from frontend
                'bank_info_display' => '', //Populated from frontend

                'amount' => $editModeData->margin_value,
                'debit_account_id' => $debit_account_id,
                'credit_account_id' => $credit_account_id,

            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'debit_account_id' => 'required',
            'credit_account_id'=>'required',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();
            $editModeData = LcCfValueMarginEntry::FindOrFail($id);

            $AccountsJournalEntry = AccountsJournalEntry::where('transaction_reference_id',$id)
                ->where('transaction_type',AccountsTransactionType::$lcMarginValue)->First();

            if($AccountsJournalEntry){
                $AccountsJournalEntry->forceDelete();

                $AccountsJournalEntryDetails = AccountsJournalEntryDetails::where('journal_entry_id',$AccountsJournalEntry->id);
                $AccountsJournalEntryDetails->forceDelete();
            }

            $this->journalDataEntry($request, $id);

            if($approval ==1){
                $this->approve($id);

                $this->debitAccountsBalanceIncDec($request);
                $this->creditAccountsBalanceIncDec($request);
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Proforma Invoice Margin Entry successfully updated!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        //
    }

    public function approve($id)
    {
        $lcMargin = LcCfValueMarginEntry::Find($id);
        if ($lcMargin->account_status == 0) {

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCMargin-$id")->First();
            $jr_data->approve_status = 1;
            $jr_data->save();

            $lcMargin->account_status = 1;
            $lcMargin->acc_updated_by = Auth::user()->id;
            $lcMargin->acc_approve_by = Auth::user()->id;
            $lcMargin->acc_approve_at = Carbon::now();
            $lcMargin->save();
        }
    }


    private function journalDataEntry($request, $id){

        $approval = $request->approve;

        $jr_status = 0;
        if($approval ==1){
            $jr_status = 1;
        }

        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => "LCMargin-$id",
            'transaction_date' => dateConvertFormtoDB($request->margin_entry_date),
            'transaction_type' => AccountsTransactionType::$lcMarginValue,
            'transaction_type_name' => AccountsTransactionType::$lcMarginValueTitle,
            'cost_center_id' => '',
            'branch_id' => '',
            'narration' => $request->narration,
            'total_debit' => $request->amount,
            'total_credit' => $request->amount,
            'approve_status' => $jr_status,
            'created_by' => Auth::user()->id,
        ];

        $result = AccountsJournalEntry::create($JournalData);

        $debitData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->debit_account_id,
            // 'remarks' => '',
            'debit_amount' => $request->amount,
        ];

        AccountsJournalEntryDetails::insert($debitData);


        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            // 'remarks' => '',
            'credit_amount' => $request->amount,
        ];

        AccountsJournalEntryDetails::insert($creditData);

    }

    public function debitAccountsBalanceIncDec(Request $request)
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

    public function creditAccountsBalanceIncDec(Request $request){

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
}
