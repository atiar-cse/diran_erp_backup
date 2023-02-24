<?php

namespace App\Http\Controllers\Accounts\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionDryerDetails;
use App\Model\Production\ProductionDryer;

use Illuminate\Support\Facades\Auth;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Repositories\CommonRepositories;
use App\Model\Accounts\AccountsJournalEntryDetails;
use DB;
use Carbon\Carbon;
use App\Lib\Enumerations\AccountsTransactionType;

class DryerVoucherController extends Controller
{
    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('production_dryers')
                ->leftJoin('users as ura', 'production_dryers.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_dryers.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_dryers.approve_by','=','urea.id')
                ->whereNull('production_dryers.deleted_at')
                ->where('production_dryers.approve_status',1)
                ->select(['production_dryers.id AS id',
                    'production_dryers.dryer_no',
                    'production_dryers.dryer_date',
                    'production_dryers.narration',
                    'production_dryers.created_by',
                    'production_dryers.updated_by',
                    'production_dryers.approve_by',
                    'production_dryers.acc_approve_by',
                    'production_dryers.acc_approve_at',
                    'production_dryers.account_status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $chartofAccountList = AccountsChartofAccounts::where('status', '1')
            ->select('id','chart_of_accounts_title','chart_of_account_code')
            ->orderBy('chart_of_account_code','ASC')
            ->get();
        return view('accounts.product_section.acc_dryer_voucher', compact('chartofAccountList'));
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
            $editModeData = ProductionDryer::FindOrFail($id);
            $editModeDetailsData = ProductionDryerDetails::with('product')->where('dryer_section_id',$id)->get();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Dryer-$id")->first();

            $dr_coa_code = '';
            $dr_coa_title = '';
            $cr_coa_code = '';
            $cr_coa_title = '';
            if ($jr_data) {

                $jr_details = AccountsJournalEntryDetails::with('coa')
                    ->where('journal_entry_id',$jr_data->id)
                    ->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $dr_coa_code = $row->coa->chart_of_account_code;
                        $dr_coa_title = $row->coa->chart_of_accounts_title;
                    }
                    if ($row->credit_amount > 0) {
                        $cr_coa_code = $row->coa->chart_of_account_code;
                        $cr_coa_title = $row->coa->chart_of_accounts_title;
                    }
                }
            }

            $data = [
                'id'    => $editModeData->id,
                'dryer_no' => $editModeData->dryer_no,
                'dryer_date' => dateConvertDBtoForm($editModeData->dryer_date),
                'narration' => $editModeData->narration,

                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_remain_qty' => $editModeData->total_remain_qty,
                'total_waste_qty' => $editModeData->total_waste_qty,
                'total_waste_weight' => $editModeData->total_waste_weight,

                'total_trans_to_glaze_qty' => $editModeData->total_trans_to_glaze_qty,
                'total_trans_to_glaze_weight' => $editModeData->total_trans_to_glaze_weight,
                'total_amount' => $editModeData->total_amount,
                'account_status' => $editModeData->account_status,

                'details'    => $editModeDetailsData,
                'dr_coa_code' => $dr_coa_code,
                'dr_coa_title' => $dr_coa_title,
                'cr_coa_code' => $cr_coa_code,
                'cr_coa_title' => $cr_coa_title
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }        
    }

    public function edit($id)
    {
        try {
            $editModeData = ProductionDryer::FindOrFail($id);
            $editModeDetailsData = ProductionDryerDetails::with('product')->where('dryer_section_id',$id)->get();
            $debit_account_id = '';
            $credit_account_id = '';

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Dryer-$id")->first();
            if ($jr_data) {

                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $debit_account_id = $row->char_of_account_id;
                    }
                    if ($row->credit_amount > 0) {
                        $credit_account_id = $row->char_of_account_id;
                    }
                }
            }
            $data = [
                'id'    => $editModeData->id,
                'dryer_no' => $editModeData->dryer_no,
                'dryer_date' => dateConvertDBtoForm($editModeData->dryer_date),
                'narration' => $editModeData->narration,

                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_remain_qty' => $editModeData->total_remain_qty,
                'total_waste_qty' => $editModeData->total_waste_qty,
                'total_waste_weight' => $editModeData->total_waste_weight,

                'total_trans_to_glaze_qty' => $editModeData->total_trans_to_glaze_qty,
                'total_trans_to_glaze_weight' => $editModeData->total_trans_to_glaze_weight,
                'total_amount' => $editModeData->total_amount,

                'details'    => $editModeDetailsData,
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
            'total_amount' => 'required',
        ], [
            'total_amount.required' => 'Required field.',
        ]);

        $app = $request->approve_status;
        if ($app == 1) {
            $this->validate($request, [
                'debit_account_id' => 'required',
                'credit_account_id' => 'required',
            ]);
        }

        try {
            DB::beginTransaction();

            //#Journal - UpdateOrCreate
            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Dryer-$id")->first();
            if ($jr_data) {

                $jr_data->narration  = $request->narration;
                $jr_data->save();

                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $row->debit_amount = $request->total_amount;
                        $row->char_of_account_id = $request->debit_account_id;
                        $row->save();
                    }
                    if ($row->credit_amount > 0) {
                        $row->credit_amount = $request->total_amount;
                        $row->char_of_account_id = $request->credit_account_id;
                        $row->save();
                    }
                }

            } else {

                if ($request->filled('debit_account_id') && $request->filled('credit_account_id')) {
                    $this->journalDataEntry($request, $id);
                }
            }


            if($app == 1) {
                $this->approve($id);

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Dryer-$id")->first();
                $jr_data->approve_status = 1;
                $jr_data->save();

                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                $this->commonRepositories->currentBalanceIncrementDecrement($jr_details);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function journalDataEntry($request, $id){

        $approval = $request->approve;

        $jr_status = 0;
        if($approval ==1){
            $jr_status = 1;
        }
        $JournalData = [
            'transaction_reference_id'  => $id, //production_requisition_for_rms id
            'transaction_reference_no'  => "Dryer-$id",
            'transaction_date'          => dateConvertFormtoDB($request->dryer_date),
            'transaction_type'          => AccountsTransactionType::$ProductionDryer,
            'transaction_type_name'     => AccountsTransactionType::$ProductionDryerTitle,

            'narration'                 => $request->narration,
            'total_debit'               => $request->total_amount,
            'total_credit'              => $request->total_amount,
            'approve_status'            => $jr_status,
            'created_by'                => Auth::user()->id,
        ];
        $result = AccountsJournalEntry::create($JournalData);

        $debitData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->debit_account_id,
            'debit_amount' => $request->total_amount,
        ];
        AccountsJournalEntryDetails::insert($debitData);

        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'credit_amount' => $request->total_amount,
        ];
        AccountsJournalEntryDetails::insert($creditData);
    }

    public function approve($id)
    {
        $appData = ProductionDryer::FindOrFail($id);
        if ($appData->account_status == 0) {

            $appData->account_status = 1;
            $appData->acc_approve_by = Auth::user()->id;
            $appData->acc_approve_at = Carbon::now();
            $appData->save();
        }
    }
}
