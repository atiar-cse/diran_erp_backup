<?php

namespace App\Http\Controllers\Accounts\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Lib\Enumerations\AccountsTransactionType;

use App\Model\Production\ProductionGlaze;
use App\Model\Production\ProductionGlazeDetails;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Repositories\CommonRepositories;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;

class GlazeVoucherController extends Controller
{
    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('production_glazes')
                ->leftJoin('users as ura', 'production_glazes.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_glazes.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_glazes.approve_by','=','urea.id')
                ->whereNull('production_glazes.deleted_at')
                ->where('production_glazes.approve_status',1)
                ->select(['production_glazes.id AS id',
                    'production_glazes.glaze_no',
                    'production_glazes.glaze_date',
                    'production_glazes.narration',
                    'production_glazes.created_by',
                    'production_glazes.updated_by',
                    'production_glazes.approve_by',
                    'production_glazes.approve_status',
                    'production_glazes.account_status',
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

        return view('accounts.product_section.acc_glaze_voucher', compact('chartofAccountList'));
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
            $editModeData = ProductionGlaze::FindOrFail($id);
            $editModeDetailsData = ProductionGlazeDetails::with('product')->where('glaze_section_id',$id)->get();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Glaze-$id")->first();

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
                'id'                        => $editModeData->id,
                'glaze_no'                  => $editModeData->glaze_no,
                'glaze_date'                => dateConvertDBtoForm($editModeData->glaze_date),
                'narration'                 => $editModeData->narration,
                'total_trans_to_kiln_qty'   => $editModeData->total_trans_to_kiln_qty,
                'total_trans_to_kiln_weight'=> $editModeData->total_trans_to_kiln_weight,
                'total_waste_qty'           => $editModeData->total_waste_qty,
                'total_waste_weight'        => $editModeData->total_waste_weight,
                'total_receive_qty'         => $editModeData->total_receive_qty,
                'total_remain_qty'          => $editModeData->total_remain_qty,
                'process_loss_percentage'   => $editModeData->process_loss_percentage,
                'process_loss_weight'       => $editModeData->process_loss_weight,
                'weight_after_process_loss' => $editModeData->weight_after_process_loss,
                'total_qty'                 => $editModeData->total_qty,
                'total_amount'              => $editModeData->total_amount,

                'details'                   => $editModeDetailsData,
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
            $editModeData = ProductionGlaze::FindOrFail($id);
            $editModeDetailsData = ProductionGlazeDetails::with('product')->where('glaze_section_id',$id)->get();

            $debit_account_id = '';
            $credit_account_id = '';

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Glaze-$id")->first();
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
                'id'                        => $editModeData->id,
                'glaze_no'                  => $editModeData->glaze_no,
                'glaze_date'                => dateConvertDBtoForm($editModeData->glaze_date),
                'narration'                 => $editModeData->narration,
                'total_trans_to_kiln_qty'   => $editModeData->total_trans_to_kiln_qty,
                'total_trans_to_kiln_weight'=> $editModeData->total_trans_to_kiln_weight,
                'total_waste_qty'           => $editModeData->total_waste_qty,
                'total_waste_weight'        => $editModeData->total_waste_weight,
                'total_receive_qty'         => $editModeData->total_receive_qty,
                'total_remain_qty'          => $editModeData->total_remain_qty,
                'process_loss_percentage'   => $editModeData->process_loss_percentage,
                'process_loss_weight'       => $editModeData->process_loss_weight,
                'weight_after_process_loss' => $editModeData->weight_after_process_loss,
                'total_qty'                 => $editModeData->total_qty,
                'total_amount'              => $editModeData->total_amount,

                'details'                   => $editModeDetailsData,
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
            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Glaze-$id")->first();
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

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Glaze-$id")->first();
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
            'transaction_reference_no'  => "Glaze-$id",
            'transaction_date'          => dateConvertFormtoDB($request->glaze_date),
            'transaction_type'          => AccountsTransactionType::$ProductionGlaze,
            'transaction_type_name'     => AccountsTransactionType::$ProductionGlazeTitle,

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
        $appData = ProductionGlaze::FindOrFail($id);
        if ($appData->account_status == 0) {

            $appData->account_status = 1;
            $appData->acc_approve_by = Auth::user()->id;
            $appData->acc_approve_at = Carbon::now();
            $appData->save();
        }
    }

}
