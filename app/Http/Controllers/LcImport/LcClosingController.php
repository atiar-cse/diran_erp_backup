<?php

namespace App\Http\Controllers\LcImport;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use App\Model\LC\LcClosing;

use App\Model\LC\LcOpeningSection;
use App\Model\LC\LcCommercialInvoiceEntry;

use App\Model\LC\LcCfValueMarginEntry;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Lib\Enumerations\AccountsTransactionType;

class LcClosingController extends Controller
{
    public function index(Request $request)
    {        
        if ($request->ajax()) {
            $query = DB::table('lc_closings')
                ->leftJoin('lc_opening_sections', 'lc_closings.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('purchase_supplier_entries', 'lc_closings.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('users as ura', 'lc_closings.created_by','=','ura.id')
                ->leftJoin('users as ured', 'lc_closings.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'lc_closings.approve_by','=','urea.id')
                ->whereNull('lc_closings.deleted_at')
                ->select(['lc_closings.id AS id',
                    'lc_closings.closing_date',
                    'lc_closings.total_cost',
                    'lc_closings.created_by',
                    'lc_closings.updated_by',
                    'lc_closings.approve_by',
                    'lc_closings.approve_status',
                    'lc_opening_sections.lc_no',
                    'lc_opening_sections.lc_total_value',
                    'lc_opening_sections.lc_opening_charges',
                    'lc_opening_sections.lc_bank_expenses',
                    'lc_opening_sections.insurance_amount',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $lcLists = LcOpeningSection::with('get_supplier','get_pi_no')->select('id','lc_no','supplier_id','proforma_invoice_id','lc_opening_charges','lc_bank_expenses','insurance_amount','lc_total_value')
                                    ->where('status',1)
                                    ->where('lc_closing_status',0)
                                    ->get();

        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();

        return view('lc.lc_section.lc_closing',compact('lcLists','chartofAccountList'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'lc_opening_info_id' => 'required',
            'closing_date' => 'required',

            // 'debit_account_id' => 'required',
            // 'credit_account_id' => 'required',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'closing_date.required' => 'The Date field is required.',

            // 'debit_account_id.required' => 'Debit Account field is required.',
            // 'credit_account_id.required' => 'Creadit Account field is required.',
        ]);

        $input = $request->all();
        // $input['total_cost'] = $request->amount;
        $input['closing_date'] = dateConvertFormtoDB($request->closing_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $lc = LcOpeningSection::FindOrFail($request->lc_opening_info_id);

            if ($lc->lc_closing_status==0) {

                $result = LcClosing::create($input);

                // $this->journalDataEntry($request, $result->id);

                if($approval ==1){
                    $this->approve($result->id);
                    // $this->debitAccountsBalanceIncDec($request);
                    // $this->creditAccountsBalanceIncDec($request);                
                }

                $lc->lc_closing_status = 1;
                $lc->save();                
            } else {
                return response()->json(['status' => 'error', 'message' => 'Already Closed']);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Closing Successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            $mm = $e->getMessage();

            return response()->json(['status' => 'error', 'message' => $mm]);
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = LcClosing::with('get_lc_no','get_supplier')->FindOrFail($id);

            $data = [
                'closing_date' => dateConvertDBtoForm($editModeData->closing_date),
                'lc_margin_percentage' => $editModeData->lc_margin_percentage,
                'narration' => $editModeData->narration,
                'total_cost' => $editModeData->total_cost,
                'landed_cost' => $editModeData->landed_cost,
                'approve_status' => $editModeData->approve_status,

                'get_lc_no'    => $editModeData->get_lc_no,                
                'get_supplier'    => $editModeData->get_supplier,                
            ];
         
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function getCommercialInvoices($lc_opening_info_id)
    {
        $data = [];
        $data['ci_lists'] = LcCommercialInvoiceEntry::with('get_latr_rows','get_custom_duty_charges','get_other_charges')
                            ->select('id','lc_opening_info_id','ci_no','total_qty','total_amount','status','approve_status','stock_enrty_status')
                                            ->where('lc_opening_info_id',$lc_opening_info_id)
                                            ->get();
        $data['lc_margin'] = LcCfValueMarginEntry::where('approve_status',1)
                                    ->where('lc_opening_info_id',$lc_opening_info_id)
                                    ->selectRaw('sum(margin_value) as total_margin')
                                    ->First();
        return response()->json(['status'=>'success','data'=>$data]);      
    }    


    public function approve($id)
    {
        $LcClosing = LcClosing::Find($id);
        if ($LcClosing->approve_status == 0) {

            $LcClosing->approve_status = 1;
            $LcClosing->approve_by = Auth::user()->id;
            $LcClosing->updated_by = Auth::user()->id;
            $LcClosing->approve_at = Carbon::now();
            $LcClosing->save();
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
            'transaction_reference_no' => "LCClosing-$id",
            'transaction_date' => dateConvertFormtoDB($request->closing_date),
            'transaction_type' => AccountsTransactionType::$lcClosingEntry,
            'transaction_type_name' => AccountsTransactionType::$lcClosingEntryTitle,
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
