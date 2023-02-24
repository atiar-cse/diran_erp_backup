<?php

namespace App\Http\Controllers\Accounts\Lc;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\LC\LcCommercialInvoiceEntry;
use App\Model\LC\LcLatrDetails;
use App\Model\LC\LcLatrEntry;
use App\Model\LC\LcOpeningSection;
use App\Model\Production\ProductionMeasureUnit;
use App\Model\Production\ProductionProducts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class LcLatrVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('lc_latr_entries')
                ->leftJoin('lc_opening_sections', 'lc_latr_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('lc_commercial_invoice_entries', 'lc_latr_entries.lc_commercial_invoice_id', '=', 'lc_commercial_invoice_entries.id')
                ->leftJoin('users', 'lc_latr_entries.created_by', '=', 'users.id')
                ->whereNull('lc_latr_entries.deleted_at')
                ->select(['lc_latr_entries.id AS id',
                    'lc_latr_entries.latr_date',
                    'lc_latr_entries.lc_margin_percentage',
                    'lc_latr_entries.total_qty',
                    'lc_latr_entries.total_amount',
                    'lc_latr_entries.latr_percentage',
                    'lc_latr_entries.latr_total_amount',
                    'lc_latr_entries.created_by',
                    'lc_latr_entries.updated_by',
                    'lc_latr_entries.approve_status',
                    'lc_latr_entries.account_status',
                    'lc_opening_sections.lc_no',
                    'lc_commercial_invoice_entries.ci_no',
                    'users.user_name',
                ]);

            return datatables()->of($query)->toJson();

        }

        $lcLists = LcOpeningSection::with('get_cnf_margin')
            ->select('id','lc_no')->where('status',1)->where('approve_status',1)
            ->where('lc_closing_status',0)->get();
        $productLists = ProductionProducts::where('status',1)->selectRaw('id,product_name,product_code,measure_unit_id')->get();
        $measureUnitList = ProductionMeasureUnit::where('status', '1')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();

        return view('accounts.lc_section.acc_latr_entry',compact('lcLists',
            'productLists','measureUnitList','chartofAccountList'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function getCommercialInvoicesByLcNo($lc_opening_info_id)
    {
        $ciLists = LcCommercialInvoiceEntry::select('id','ci_no')
            ->where('lc_opening_info_id',$lc_opening_info_id)
            ->where('approve_status',1)
            ->get();
        return $ciLists;
    }

    public function show($id)
    {
        try {
            $editModeData = LcLatrEntry::with('get_lc_no','get_ci_no')->FindOrFail($id);
            $editModeDetailsData = LcLatrDetails::with('get_product','get_measure_unit')->where('lc_latr_payment_entry_id',$id)->get();

            $data = [
                'id'                            => $editModeData->id,
                'lc_opening_info_id'            => $editModeData->lc_opening_info_id,
                'lc_commercial_invoice_id'      => $editModeData->lc_commercial_invoice_id,
                'latr_date'                     => dateConvertDBtoForm($editModeData->latr_date),
                'lc_margin_percentage'          => $editModeData->lc_margin_percentage,
                'narration'                     => $editModeData->narration,
                'total_qty'                     => $editModeData->total_qty,
                'total_amount'                  => $editModeData->total_amount,
                'latr_percentage'               => $editModeData->latr_percentage,
                'bank_percentage'               => $editModeData->bank_percentage,
                'bank_percentage_amount'        => $editModeData->bank_percentage_amount,
                'latr_total_amount'             => $editModeData->latr_total_amount,
                'status'                        => $editModeData->status,
                'approve_status'                => $editModeData->approve_status,
                'details'                       => $editModeDetailsData,

                'get_lc_no'                     => $editModeData->get_lc_no,
                'get_ci_no'                     => $editModeData->get_ci_no,
            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcLatrEntry::FindOrFail($id);
            $editModeDetailsData = LcLatrDetails::where('lc_latr_payment_entry_id',$id)->get();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCLatr-$id")->first();

            if($jr_data){
                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                $narration = $jr_data->narration;
                $debit_account_id = '';
                $credit_account_id = '';
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $debit_account_id = $row->char_of_account_id;
                    }
                    if ($row->credit_amount > 0) {
                        $credit_account_id = $row->char_of_account_id;
                    }
                }
            }
            else{
                $debit_account_id = '';
                $credit_account_id = '';
                $narration = $editModeData->narration;
            }


            $data = [
                'id'                        => $editModeData->id,
                'lc_opening_info_id'        => $editModeData->lc_opening_info_id,
                'lc_commercial_invoice_id'  => $editModeData->lc_commercial_invoice_id,
                'latr_date'                 => dateConvertDBtoForm($editModeData->latr_date),
                'lc_margin_percentage'      => $editModeData->lc_margin_percentage,
                'bank_latr_no'              => $editModeData->bank_latr_no,

                'total_qty'                 => $editModeData->total_qty,
                'total_amount'              => $editModeData->total_amount,
                'latr_percentage'           => $editModeData->latr_percentage,

                'bank_percentage'           => $editModeData->bank_percentage,
                'bank_percentage_amount'    => $editModeData->bank_percentage_amount,

                'latr_total_amount'         => $editModeData->latr_total_amount,
                'status'                    => $editModeData->status,

                'details'                   => $editModeDetailsData,

                'amount'                    => $editModeData->latr_total_amount,
                'debit_account_id'          => $debit_account_id,
                'credit_account_id'         => $credit_account_id,

                'narration'                 => $narration,
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
            'credit_account_id' => 'required',
        ], [
            'debit_account_id.required' => 'Debit Account field is required.',
            'credit_account_id.required' => 'Creadit Account field is required.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCLatr-$id")->first();

            if($jr_data){
                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $row->char_of_account_id = $request->debit_account_id;
                        $row->save();
                    }
                    if ($row->credit_amount > 0) {
                        $row->char_of_account_id = $request->credit_account_id;
                        $row->save();
                    }
                }
            }else{
                $this->journalDataEntry($request, $id);
            }



            if($approval ==1){
                $this->approve($id);

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCLatr-$id")->First();
                $jr_data->approve_status = 1;
                $jr_data->save();

                $this->debitAccountsBalanceIncDec($request);
                $this->creditAccountsBalanceIncDec($request);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'LATR Payment successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            //echo $e->getMessage();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);

        }
    }

    public function destroy($id)
    {
        //
    }

    public function approve($id)
    {
        $lcLatr = LcLatrEntry::FindOrFail($id);
        if ($lcLatr->account_status == 0) {

            $lcLatr->account_status = 1;
            $lcLatr->updated_by = Auth::user()->id;
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
            'transaction_reference_no' => "LCLatr-$id",
            'transaction_date' => dateConvertFormtoDB($request->latr_date),
            'transaction_type' => AccountsTransactionType::$lcLatrValue,
            'transaction_type_name' => AccountsTransactionType::$lcLatrTitle,
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

    public function debitAccountsBalanceIncDec(Request $request)
    {
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
