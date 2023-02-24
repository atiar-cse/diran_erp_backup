<?php

namespace App\Http\Controllers\Accounts\Lc;


use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\LC\LcInsurance;
use App\Model\LC\LcOpeningSection;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Lib\Enumerations\AccountsTransactionType;
use Auth;
use DB;


class LcInsuranceVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('lc_insurances')
                ->leftJoin('lc_opening_sections', 'lc_insurances.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('users as ura', 'lc_insurances.created_by','=','ura.id')
                ->leftJoin('users as uredac', 'lc_insurances.acc_updated_by','=','uredac.id')
                ->leftJoin('users as ureac', 'lc_insurances.acc_approve_by','=','ureac.id')
                ->where('lc_insurances.approve_status',1)
                ->select(['lc_insurances.id AS id',
                    'lc_insurances.insurance_company',
                    'lc_insurances.insurance_date',
                    'lc_insurances.insurance_no',
                    'lc_insurances.insurance_amount',
                    'lc_insurances.created_by',
                    'lc_insurances.acc_updated_by',
                    'lc_insurances.acc_approve_by',
                    'lc_insurances.account_status',
                    'lc_opening_sections.lc_no',
                    'lc_opening_sections.lc_total_value',
                    'ura.user_name as ad_name',
                    'uredac.user_name as ac_ed_name',
                    'ureac.user_name as ac_ap_name'
                ]);

            return datatables()->of($query)->toJson();

        }

        $lcLists = LcOpeningSection::with('get_supplier')
            ->where('status',1)
            ->where('approve_status',1)
            ->where('lc_closing_status',0)
            ->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();

        return view('accounts.lc_section.acc_lc_insurance',compact('lcLists','chartofAccountList'));
    }


    public function show($id)
    {
        try {
            $editModeData = LcInsurance::with('get_lc_no')->FindOrFail($id);

            return response()->json(['status'=>'success','data'=>$editModeData]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcInsurance::with('get_lc_no')->FindOrFail($id);

            $jr_data = AccountsJournalEntry::where('transaction_reference_id',$id)
                ->where('transaction_type',AccountsTransactionType::$lcInsuranceCharge)->First();
            $debit_account_id = '';
            $credit_account_id = '';
            $narration = '';

            if($jr_data){
                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->Get();
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
                'lc_no' => $editModeData->get_lc_no->lc_no,
                'insurance_date' => dateConvertDBtoForm($editModeData->insurance_date),
                'insurance_company' => $editModeData->insurance_company,
                'insurance_no' => $editModeData->insurance_no,
                'insurance_amount' => $editModeData->insurance_amount,
                'narration' => $narration,

                'supplier_info_display' => '', //Populated from frontend
                'lc_value' => $editModeData->get_lc_no->lc_total_value, //Populated from frontend

                'amount' => $editModeData->insurance_amount,
                'debit_account_id' => $debit_account_id,
                'credit_account_id' => $credit_account_id,

            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            echo $e->getMessage();
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'debit_account_id' => 'required',
            'credit_account_id'=>'required',
        ]);

        /* $input = $request->all();
        $input['insurance_date'] = dateConvertFormtoDB($request->insurance_date);
        $input['updated_by'] = Auth::user()->id;*/

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $AccountsJournalEntry = AccountsJournalEntry::where('transaction_reference_id',$id)
                ->where('transaction_type',AccountsTransactionType::$lcInsuranceCharge)->First();

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
            return response()->json(['status' => 'success', 'message' => 'LC Insurance successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            //print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }



    public function approve($id)
    {
        $row = LcInsurance::FindOrFail($id);
        if ($row->account_status == 0) {

            $row->account_status = 1;
            $row->acc_updated_by = Auth::user()->id;
            $row->acc_approve_by = Auth::user()->id;
            $row->acc_approve_at = Carbon::now();
            $row->save();
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
            'transaction_reference_no' => $request->lc_no,
            'transaction_date' => dateConvertFormtoDB($request->insurance_date),
            'transaction_type' => AccountsTransactionType::$lcInsuranceCharge,
            'transaction_type_name' => AccountsTransactionType::$lcInsuranceChargeTitle,
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
            'remarks' => $request->narration,
            'debit_amount' => $request->amount,
        ];
        AccountsJournalEntryDetails::insert($debitData);

        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'remarks' => $request->narration,
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
