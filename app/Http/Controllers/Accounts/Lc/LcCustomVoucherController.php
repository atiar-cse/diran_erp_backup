<?php

namespace App\Http\Controllers\Accounts\Lc;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\LC\LcCustomDutyChargeDetails;
use App\Model\LC\LcCustomDutyChargeEntry;
use App\Model\LC\LcCustomDutyCostNameEntry;
use App\Model\LC\LcOpeningSection;
use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseSupplierEntry;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Lib\Enumerations\AccountsTransactionType;
use DB;

class LcCustomVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_custom_duty_charge_entries')
                ->leftJoin('lc_opening_sections', 'lc_custom_duty_charge_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('lc_commercial_invoice_entries', 'lc_custom_duty_charge_entries.lc_commercial_invoice_id', '=', 'lc_commercial_invoice_entries.id')
                ->leftJoin('lc_custom_duty_cost_name_entries', 'lc_custom_duty_charge_entries.custom_duty_cost_id', '=', 'lc_custom_duty_cost_name_entries.id')
                ->leftJoin('users as ura', 'lc_custom_duty_charge_entries.created_by','=','ura.id')
                ->leftJoin('users as uredac', 'lc_custom_duty_charge_entries.acc_updated_by','=','uredac.id')
                ->leftJoin('users as ureac', 'lc_custom_duty_charge_entries.acc_approve_by','=','ureac.id')
                ->whereNull('lc_custom_duty_charge_entries.deleted_at')
                ->where('lc_custom_duty_charge_entries.approve_status',1)
                ->select(['lc_custom_duty_charge_entries.id AS id',
                    'lc_custom_duty_charge_entries.custom_duty_date',
                    'lc_custom_duty_charge_entries.total_cost',
                    'lc_custom_duty_charge_entries.created_by',
                    'lc_custom_duty_charge_entries.acc_updated_by',
                    'lc_custom_duty_charge_entries.acc_approve_by',
                    'lc_custom_duty_charge_entries.account_status',
                    'lc_opening_sections.lc_no',
                    'lc_commercial_invoice_entries.ci_no',
                    'lc_custom_duty_cost_name_entries.duty_cost_name',
                    'ura.user_name as ad_name',
                    'uredac.user_name as ac_ed_name',
                    'ureac.user_name as ac_ap_name'
                ]);

            return datatables()->of($query)->toJson();

        }

        $lcLists = LcOpeningSection::select('id','lc_no')->where('status',1)->where('approve_status',1)
            ->where('lc_closing_status',0)->get();
        $lcCustomDutyNameLists = LcCustomDutyCostNameEntry::where('status',1)->selectRaw('id,duty_cost_name')->get();
        $productLists = ProductionProducts::where('status',1)->selectRaw('id,product_name,product_code')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();
        $supplierList = PurchaseSupplierEntry::where('is_importer',1)->get();
        return view('accounts.lc_section.acc_custom_duty_charge',compact('lcLists','lcCustomDutyNameLists','productLists','chartofAccountList','supplierList'));
    }


    public function show($id)
    {
        try {
            $editModeData = LcCustomDutyChargeEntry::with('get_lc_no','get_ci_no','get_custom_duty','get_cnf_agent')->FindOrFail($id);
            $editModeDetailsData = LcCustomDutyChargeDetails::with('get_product')->where('lc_custom_duty_entry_id',$id)->get();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCCustomDC-$id")->first();

            $data = [
                'id'    => $editModeData->id,
                'custom_duty_date' => dateConvertDBtoForm($editModeData->custom_duty_date),
                'total_cost'       => $editModeData->total_cost,
                'narration'        => $jr_data? $jr_data->narration : $editModeData->narration,
                'status'           => $editModeData->status,
                'approve_status'   => $editModeData->account_status,

                'get_lc_no'        => $editModeData->get_lc_no,
                'get_ci_no'        => $editModeData->get_ci_no,
                'get_custom_duty'  => $editModeData->get_custom_duty,
                'get_cnf_agent'    => $editModeData->get_cnf_agent ? $editModeData->get_cnf_agent->purchase_supplier_name:'',
                'details'          => $editModeDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcCustomDutyChargeEntry::with('get_lc_no','get_ci_no')->FindOrFail($id);
            $editModeDetailsData = LcCustomDutyChargeDetails::where('lc_custom_duty_entry_id',$id)->get();
            $lcCustomDutyNameLists = LcCustomDutyCostNameEntry::where('status',1)->where('id',$editModeData->custom_duty_cost_id)
                ->selectRaw('id,chart_ac_id')->first();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCCustomDC-$id")->first();

            if($jr_data){
                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $debit_account_id = $row->char_of_account_id;
                    }
                    if ($row->credit_amount > 0) {
                        $credit_account_id = $row->char_of_account_id;
                    }
                }
                $narration = $jr_data->narration;
            }else{
                $debit_account_id = $lcCustomDutyNameLists ? $lcCustomDutyNameLists->chart_ac_id : '';
                $credit_account_id = '';
                $narration = $editModeData->narration;
            }


            $data = [
                'id'    => $editModeData->id,
                'lc_opening_info_id'       => $editModeData->get_lc_no->lc_no,
                'lc_commercial_invoice_id' => $editModeData->get_ci_no->ci_no,
                'custom_duty_date'         => dateConvertDBtoForm($editModeData->custom_duty_date),
                'custom_duty_cost_id'      => $editModeData->custom_duty_cost_id,
                'total_cost'               => $editModeData->total_cost,
                'cnf_agent_id'             => $editModeData->cnf_agent_id,
                'narration'                => $narration,
                'status'                   => $editModeData->status,

                'details'                  => $editModeDetailsData,
                'amount'                   => $editModeData->total_cost,
                'debit_account_id'         => $debit_account_id,
                'credit_account_id'        => $credit_account_id,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'debit_account_id'  => 'required',
            'credit_account_id' => 'required',
        ], [
            'debit_account_id.required' => 'The Debit Account field is required.',
            'credit_account_id.required' => 'The Credit Account field is required.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCCustomDC-$id")->First();
            if($jr_data){

                $jr_data->forceDelete();
                $AccountsJournalEntryDetails = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id);
                $AccountsJournalEntryDetails->forceDelete();
            }

            $this->journalDataEntry($request, $id);

            if($approval ==1){
                $this->approve($id);

                $this->debitAccountsBalanceIncDec($request);
                $this->creditAccountsBalanceIncDec($request);
            }


            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Custom Duty Charge successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function approve($id)
    {
        $lcLatr = LcCustomDutyChargeEntry::Find($id);
        if ($lcLatr->account_status == 0) {

            $lcLatr->account_status = 1;
            $lcLatr->acc_updated_by = Auth::user()->id;
            $lcLatr->acc_approve_by = Auth::user()->id;
            $lcLatr->acc_approve_at = Carbon::now();
            $lcLatr->save();
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
            'transaction_reference_no' => "LCCustomDC-$id",
            'transaction_date' => dateConvertFormtoDB($request->custom_duty_date),
            'transaction_type' => AccountsTransactionType::$lcCustomDutyCharge,
            'transaction_type_name' => AccountsTransactionType::$lcCustomDutyChargeTitle,
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
            'debit_amount' => $request->amount,
        ];
        AccountsJournalEntryDetails::insert($debitData);

        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'credit_amount' => $request->amount,
        ];
        AccountsJournalEntryDetails::insert($creditData);

    }

    public function debitAccountsBalanceIncDec(Request $request){

        $debitAccountData = AccountsChartofAccounts::FindOrFail($request->debit_account_id);
        $debitCode = mb_substr($debitAccountData->chart_of_account_code, 0, 1);
        $debitMainCode = AccountsMainCode::where('main_code', $debitCode)->first();

        if ($debitMainCode->main_code_title == 'Assets') {
            AccountsChartofAccounts::where('id', $request->debit_account_id)->increment('current_balance', $request->amount);
        } elseif ($debitMainCode->main_code_title == 'Expense') {
            AccountsChartofAccounts::where('id', $request->debit_account_id)->increment('current_balance', $request->amount);
        } elseif ($debitMainCode->main_code_title == 'Income') {
            AccountsChartofAccounts::where('id', $request->debit_account_id)->decrement('current_balance', $request->amount);
        } elseif ($debitMainCode->main_code_title == 'Liabilities') {
            AccountsChartofAccounts::where('id', $request->debit_account_id)->decrement('current_balance', $request->amount);
        } elseif ($debitMainCode->main_code_title == 'Equity') { //Equity / Capital
            AccountsChartofAccounts::where('id', $request->debit_account_id)->decrement('current_balance', $request->amount);
        }
    }

    public function creditAccountsBalanceIncDec(Request $request){
        $creditAccountData = AccountsChartofAccounts::FindOrFail($request->credit_account_id);
        $creditCode = mb_substr($creditAccountData->chart_of_account_code, 0, 1);
        $creditMainCode = AccountsMainCode::where('main_code', $creditCode)->first();

        if ($creditMainCode->main_code_title == 'Assets') {
            AccountsChartofAccounts::where('id', $request->credit_account_id)->decrement('current_balance', $request->amount);
        } elseif ($creditMainCode->main_code_title == 'Expense') {
            AccountsChartofAccounts::where('id', $request->credit_account_id)->decrement('current_balance', $request->amount);
        } elseif ($creditMainCode->main_code_title == 'Income') {
            AccountsChartofAccounts::where('id', $request->credit_account_id)->increment('current_balance', $request->amount);
        } elseif ($creditMainCode->main_code_title == 'Liabilities') {
            AccountsChartofAccounts::where('id', $request->credit_account_id)->increment('current_balance', $request->amount);
        } elseif ($creditMainCode->main_code_title == 'Equity') { //Equity / Capital
            AccountsChartofAccounts::where('id', $request->credit_account_id)->increment('current_balance', $request->amount);
        }
    }
}
