<?php

namespace App\Http\Controllers\Accounts\Lc;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\LC\LcCostNameCategory;
use App\Model\LC\LcCostNameEntry;
use App\Model\LC\LcOpeningSection;
use App\Model\LC\LcOthersChargeDetails;
use App\Model\LC\LcOthersChargeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use DB;

class LcOtherVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_others_charge_entries')
                ->leftJoin('lc_opening_sections', 'lc_others_charge_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('lc_commercial_invoice_entries', 'lc_others_charge_entries.lc_commercial_invoice_id', '=', 'lc_commercial_invoice_entries.id')
                ->leftJoin('lc_cost_name_categories', 'lc_others_charge_entries.cost_category_id', '=', 'lc_cost_name_categories.id')
                ->leftJoin('users as ura', 'lc_others_charge_entries.created_by','=','ura.id')
                ->leftJoin('users as uredac', 'lc_others_charge_entries.acc_updated_by','=','uredac.id')
                ->leftJoin('users as ureac', 'lc_others_charge_entries.acc_approve_by','=','ureac.id')
                ->whereNull('lc_others_charge_entries.deleted_at')
                ->select(['lc_others_charge_entries.id AS id',
                    'lc_others_charge_entries.others_charge_date',
                    'lc_others_charge_entries.total_cost',
                    'lc_others_charge_entries.created_by',
                    'lc_others_charge_entries.acc_updated_by',
                    'lc_others_charge_entries.acc_approve_by',
                    'lc_others_charge_entries.approve_status',
                    'lc_others_charge_entries.account_status',
                    'lc_opening_sections.lc_no',
                    'lc_commercial_invoice_entries.ci_no',
                    'lc_cost_name_categories.cost_category_name',
                    'ura.user_name as ad_name',
                    'uredac.user_name as ac_ed_name',
                    'ureac.user_name as ac_ap_name'
                ]);

            return datatables()->of($query)->toJson();

        }

        $lcLists = LcOpeningSection::select('id','lc_no')
            ->where('status',1)
            ->where('approve_status',1)
            ->where('lc_closing_status',0)
            ->get();
        $lcCostNameCategoryList = LcCostNameCategory::where('status',1)->selectRaw('id,cost_category_name')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();

        return view('accounts.lc_section.acc_others_charge_entry',compact('lcLists','lcCostNameCategoryList','chartofAccountList'));
    }


    public function show($id)
    {
        try {
            $editModeData = LcOthersChargeEntry::with('get_lc_no','get_ci_no','get_cost_cat')->FindOrFail($id);
            $DetailsData = LcOthersChargeDetails::with('get_cost_name')->where('lc_other_charge_entry_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'others_charge_date' => dateConvertDBtoForm($editModeData->others_charge_date),
                'total_cost' => $editModeData->total_cost,
                'narration' => $editModeData->narration,
                'approve_status' => $editModeData->approve_status,
                'details'    => $DetailsData,

                'get_lc_no'    => $editModeData->get_lc_no,
                'get_ci_no'    => $editModeData->get_ci_no,
                'get_cost_cat'    => $editModeData->get_cost_cat,

            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcOthersChargeEntry::FindOrFail($id);
            $DetailsData = LcOthersChargeDetails::where('lc_other_charge_entry_id',$id)->get();

            $jr_data = AccountsJournalEntry::where('transaction_reference_id',$id)
                ->where('transaction_type',AccountsTransactionType::$lcOtherCharge)->get();

            $dataFormat = [];
            $debit_account_id = '';
            if(count($jr_data)>0){
                //$jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                foreach ($DetailsData as $row) {
                    $serial = 'LCOtherCharge-'.$id.'-'.$row->lc_cost_name_id;

                    $costName = LcCostNameEntry::FindOrFail($row->lc_cost_name_id);

                    $coa = DB::select(DB::raw("
                            SELECT jd.char_of_account_id FROM accounts_journal_entry_details jd 
                            LEFT JOIN accounts_journal_entries j ON (j.id = jd.journal_entry_id) 
                            WHERE jd.credit_amount!=0 AND 
                            j.transaction_reference_no = '$serial'"));
                    /*echo $coa;*/


                    $doa = DB::select(DB::raw("
                            SELECT jd.char_of_account_id FROM accounts_journal_entry_details jd 
                            LEFT JOIN accounts_journal_entries j ON (j.id = jd.journal_entry_id) 
                            WHERE jd.debit_amount!=0 AND 
                            j.transaction_reference_no = '$serial'"));



                    $dataFormat[] = [
                        'id'                       => $row->id,
                        'lc_other_charge_entry_id' => $row->lc_other_charge_entry_id,
                        'lc_cost_name_id'          => $row->lc_cost_name_id,
                        'lc_cost_name'             => $costName ? $costName->cost_name:'',
                        'credit_account_id'        => $coa[0]->char_of_account_id,
                        'debit_account_id'         => $doa[0]->char_of_account_id,
                        'amount'                   => $row->cost_value,
                        'remarks'                  => $row->remarks,
                    ];
                }

            }else{
                foreach ($DetailsData as $value) {
                    $costName = LcCostNameEntry::FindOrFail($value->lc_cost_name_id);

                    $dataFormat[] = [
                        'id'                       => $value->id,
                        'lc_other_charge_entry_id' => $value->lc_other_charge_entry_id,
                        'lc_cost_name_id'          => $value->lc_cost_name_id ,
                        'lc_cost_name'             => $costName ? $costName->cost_name:'',
                        'credit_account_id'        => $costName ? $costName->chart_ac_id : '',
                        'debit_account_id'         => '',
                        'amount'                   => $value->cost_value,
                        'remarks'                  => '',
                    ];


                }
                //dd($dataFormat);
            }

            //dd($dataFormat);

            $data = [
                'id'    => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'lc_commercial_invoice_id' => $editModeData->lc_commercial_invoice_id,
                'cost_category_id' => $editModeData->cost_category_id,
                'others_charge_date' => dateConvertDBtoForm($editModeData->others_charge_date),
                'total_cost' => $editModeData->total_cost,
                'narration' => $editModeData->narration,
                'status' => $editModeData->status,
                'details'    => $dataFormat,
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
            'details.*.debit_account_id' => 'required',
            'details.*.credit_account_id' => 'required',
        ], [
            'details.*.debit_account_id.required' => 'Required field.',
            'details.*.credit_account_id.required' => 'Required field.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $jr_data =AccountsJournalEntry::where('transaction_reference_id',$id)
                ->where('transaction_type',AccountsTransactionType::$lcOtherCharge)->forceDelete();

            $date = dateConvertFormtoDB($request->others_charge_date);
            $details = $request->details;
            $count = count($details);
            for ($i = 0; $i < $count; $i++) {
                $sr = $id."-".$request->details[$i]['lc_cost_name_id'];

                $this->journalDataEntry($date, $details[$i], $sr);
                if ($approval ==1) {
                    $this->debitAccountsBalanceIncDec($details[$i]['debit_account_id'],$details[$i]['amount']);
                    $this->creditAccountsBalanceIncDec($details[$i]['credit_account_id'],$details[$i]['amount']);
                }
            }

           if($approval ==1){
                $this->approve($id);
                AccountsJournalEntry::where('transaction_reference_id',$id)
                    ->where('transaction_type',AccountsTransactionType::$lcOtherCharge)
                    ->update(['approve_status'=>1,'updated_by'=>Auth::id()]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Other Charge successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Happen!']);

        }
    }



    public function approve($id)
    {
        $lcLatr = LcOthersChargeEntry::Find($id);
        if ($lcLatr->account_status == 0) {
            $lcLatr->account_status = 1;
            $lcLatr->acc_approve_by = Auth::user()->id;
            $lcLatr->acc_updated_by = Auth::user()->id;
            $lcLatr->acc_approve_at = Carbon::now();
            $lcLatr->save();
        }
    }

    private function journalDataEntry($date,$dt, $id){

        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => "LCOtherCharge-$id",
            'transaction_date' => $date,
            'transaction_type' => AccountsTransactionType::$lcOtherCharge,
            'transaction_type_name' => AccountsTransactionType::$lcOtherChargeTitle,
            'cost_center_id' => '',
            'branch_id' => '',
            'narration' => $dt['remarks'],
            'total_debit' => $dt['amount'],
            'total_credit' => $dt['amount'],
            'approve_status' => 0,
            'created_by' => Auth::user()->id,
        ];
        $result = AccountsJournalEntry::create($JournalData);

        $debitData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' =>  $dt['debit_account_id'],
            'debit_amount' => $dt['amount'],
        ];
        AccountsJournalEntryDetails::insert($debitData);

        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $dt['credit_account_id'],
            'credit_amount' => $dt['amount'],
        ];
        AccountsJournalEntryDetails::insert($creditData);

    }

    public function debitAccountsBalanceIncDec($dr, $amount)
    {
        $debitAccountData = AccountsChartofAccounts::FindOrFail($dr);
        $debitCode = mb_substr($debitAccountData->chart_of_account_code, 0, 1);
        $debitMainCode = AccountsMainCode::where('main_code', $debitCode)->first();

        if ($debitMainCode->main_code_title == 'Assets') {
            AccountsChartofAccounts::where('id', $dr)->increment('current_balance', $amount);
        } elseif ($debitMainCode->main_code_title == 'Expense') {
            AccountsChartofAccounts::where('id', $dr)->increment('current_balance', $amount);
        } elseif ($debitMainCode->main_code_title == 'Income') {
            AccountsChartofAccounts::where('id', $dr)->decrement('current_balance', $amount);
        } elseif ($debitMainCode->main_code_title == 'Liabilities') {
            AccountsChartofAccounts::where('id', $dr)->decrement('current_balance', $amount);
        } elseif ($debitMainCode->main_code_title == 'Equity') { //Equity / Capital
            AccountsChartofAccounts::where('id', $dr)->decrement('current_balance', $amount);
        }
    }

    public function creditAccountsBalanceIncDec($cr, $amount){

        $creditAccountData = AccountsChartofAccounts::FindOrFail($cr);
        $creditCode = mb_substr($creditAccountData->chart_of_account_code, 0, 1);
        $creditMainCode = AccountsMainCode::where('main_code', $creditCode)->first();

        if ($creditMainCode->main_code_title == 'Assets') {
            AccountsChartofAccounts::where('id', $cr)->decrement('current_balance', $amount);
        } elseif ($creditMainCode->main_code_title == 'Expense') {
            AccountsChartofAccounts::where('id', $cr)->decrement('current_balance', $amount);
        } elseif ($creditMainCode->main_code_title == 'Income') {
            AccountsChartofAccounts::where('id', $cr)->increment('current_balance', $amount);
        } elseif ($creditMainCode->main_code_title == 'Liabilities') {
            AccountsChartofAccounts::where('id', $cr)->increment('current_balance', $amount);
        } elseif ($creditMainCode->main_code_title == 'Equity') { //Equity / Capital
            AccountsChartofAccounts::where('id', $cr)->increment('current_balance', $amount);
        }
    }
}
