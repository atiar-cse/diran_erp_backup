<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Payroll\PayrollBonusProcess;
use App\Model\Payroll\PayrollBonusSummery;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Lib\Enumerations\AccountsTransactionType;
use DB;

class PayrollBonusSummeryController extends Controller
{

    public function index(Request $request)
    { 
        if ($request->ajax()) {
            return PayrollBonusSummery::with('get_debit','get_credit')->orderBy('id','desc')->paginate($request->perPage);
        }
        $chartOfAccountList = AccountsChartofAccounts::where('status', '1')->get();
        return view('payroll.payroll_section.payroll_bonus__summery',compact('chartOfAccountList'));
    }


    public function calculateTotalBonus(){

        $month = date('Y-m-01');

        $totalEmployeeBonus = PayrollBonusProcess::select(DB::raw("SUM(bonus_amount) as payable_total_bonus "))->where('bonus_month',$month)->first();
        return $totalEmployeeBonus->payable_total_bonus;


    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'payroll_bonus_month' =>'required',
            'payable_total_bonus' =>'required',
            'debit_account_id'    =>'required',
            'credit_account_id'   =>'required',

        ]);

        $data = [
            'payroll_bonus_month' => $request->payroll_bonus_month,
            'payable_total_bonus' => $request->payable_total_bonus,
            'debit_account_id'    => $request->debit_account_id,
            'credit_account_id'   => $request->credit_account_id,
            'created_by'          => Auth::user()->id,

        ];

        $approval = $request->approve;
        try {
            DB::beginTransaction();

            $bonusResult = PayrollBonusSummery::create($data);

            $this->journalDataEntry($request, $bonusResult->id);

            if($approval ==1){
                $this->approve($bonusResult->id);

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
            return response()->json(['status' => 'success', 'message' => 'Bonus Summery Process  successfully saved!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {

    }


    public function edit($id)
    {
        try {
            $editModeData = PayrollBonusSummery::FindOrFail($id);
            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Bonus-$id")->First();

            $data = [
                'id'                  => $editModeData->id,
                'payroll_bonus_month' => $editModeData->payroll_bonus_month,
                'payable_total_bonus' => $editModeData->payable_total_bonus,
                'debit_account_id'    => $editModeData->debit_account_id,
                'credit_account_id'   => $editModeData->credit_account_id,
                'amount'              => $jr_data->total_credit,
            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }

    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'payroll_bonus_month' =>'required',
            'payable_total_bonus' =>'required',
            'debit_account_id'    =>'required',
            'credit_account_id'   =>'required',

        ]);

        $data = [
            'payroll_bonus_month' => $request->payroll_bonus_month,
            'payable_total_bonus' => $request->payable_total_bonus,
            'debit_account_id'    => $request->debit_account_id,
            'credit_account_id'   => $request->credit_account_id,

        ];


        $approval     = $request->approve;

        try {
            DB::beginTransaction();

            $editModeData = PayrollBonusSummery::FindOrFail($id);
            $editModeData->update($data);

            if($approval ==1){
                $this->approve($id);

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Bonus-$id")->First();
                $jr_data->approve_status = 1;
                $jr_data->save();

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
            return response()->json(['status' => 'success', 'message' => 'Bonus Summery Process successfully updated!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {

    }

    public function approve($id)
    {
        $bonusApprovedId = PayrollBonusSummery::Find($id);
        if ($bonusApprovedId->approve_status == 0) {
            $bonusApprovedId->approve_status = 1;
            $bonusApprovedId->save();
        }
    }

    private function journalDataEntry($request, $id){

        $approval = $request->approve;

        $jr_status = 0;
        if($approval ==1){
            $jr_status = 1;
        }


        $date = date('Y-m-d');

        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' =>  "Bonus-$id",
            'transaction_date'         => $request->payroll_bonus_month,
            'transaction_type'         => AccountsTransactionType::$bonusSummeryEntry,
            'transaction_type_name'    => AccountsTransactionType::$bonusSummeryEntryTitle,
            'cost_center_id'           => '',
            'branch_id'                => '',
            'narration'                => $request->narration,
            'total_debit'              => $request->amount,
            'total_credit'             => $request->amount,
            'approve_status'           => $jr_status,
            'created_by'               => Auth::user()->id,
        ];

        $result = AccountsJournalEntry::create($JournalData);

        $debitData =[
            'journal_entry_id'   => $result->id,
            'char_of_account_id' => $request->debit_account_id,
            'remarks'            => 'Debit',
            'debit_amount'       => $request->amount,
        ];

        AccountsJournalEntryDetails::insert($debitData);


        $creditData =[
            'journal_entry_id'   => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'remarks'            => 'Credit',
            'credit_amount'      => $request->amount,
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
