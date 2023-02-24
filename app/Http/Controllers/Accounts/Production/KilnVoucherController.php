<?php

namespace App\Http\Controllers\Accounts\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Lib\Enumerations\AccountsTransactionType;

use App\Model\Production\ProductionKiln;
use App\Model\Production\ProductionKilnDetails;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Repositories\CommonRepositories;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;

class KilnVoucherController extends Controller
{
    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('production_kilns')
                ->leftJoin('users as ura', 'production_kilns.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_kilns.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_kilns.approve_by','=','urea.id')
                ->whereNull('production_kilns.deleted_at')
                ->where('production_kilns.approve_status',1)
                ->select(['production_kilns.id AS id',
                    'production_kilns.kiln_no',
                    'production_kilns.kiln_date',
                    'production_kilns.narration',
                    'production_kilns.created_by',
                    'production_kilns.updated_by',
                    'production_kilns.approve_by',
                    'production_kilns.approve_status',
                    'production_kilns.account_status',
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
        return view('accounts.product_section.acc_kiln_voucher', compact('chartofAccountList'));
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
            $editModeData = ProductionKiln::FindOrFail($id);
            $editModeDetailsData = ProductionKilnDetails::with('product')->where('kiln_section_id',$id)->get();
            
            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Kiln-$id")->first();

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
                'kiln_no' => $editModeData->kiln_no,
                'kiln_date' => dateConvertDBtoForm($editModeData->kiln_date),
                'narration' => $editModeData->narration,
                'total_trans_to_hv_qty' => $editModeData->total_trans_to_hv_qty,
                'total_trans_weight' => $editModeData->total_trans_weight,
                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_remain_qty' => $editModeData->total_remain_qty,                
                'total_reject_qty' => $editModeData->total_reject_qty,
                'total_reject_weight' => $editModeData->total_reject_weight,
                'total_amount'        => $editModeData->total_amount,
                'account_status'        => $editModeData->account_status,

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
            $editModeData = ProductionKiln::FindOrFail($id);
            $editModeDetailsData = ProductionKilnDetails::with('product')->where('kiln_section_id',$id)->get();        

            $debit_account_id = '';
            $credit_account_id = '';

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Kiln-$id")->first();
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
                'kiln_no' => $editModeData->kiln_no,
                'kiln_date' => dateConvertDBtoForm($editModeData->kiln_date),
                'narration' => $editModeData->narration,
                'total_trans_to_hv_qty' => $editModeData->total_trans_to_hv_qty,
                'total_trans_weight' => $editModeData->total_trans_weight,
                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_remain_qty' => $editModeData->total_remain_qty,                
                'total_reject_qty' => $editModeData->total_reject_qty,
                'total_reject_weight' => $editModeData->total_reject_weight,
                'total_amount'        => $editModeData->total_amount,

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
            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Kiln-$id")->first();
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

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Kiln-$id")->first();
                $jr_data->approve_status = 1;
                $jr_data->save();

                $this->approve($id,$jr_data->id);

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
            'transaction_reference_no'  => "Kiln-$id",
            'transaction_date'          => dateConvertFormtoDB($request->kiln_date),
            'transaction_type'          => AccountsTransactionType::$ProductionKiln,
            'transaction_type_name'     => AccountsTransactionType::$ProductionKilnTitle,

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

    public function approve($id,$jr_data_id)
    {
        $appData = ProductionKiln::FindOrFail($id);

        if ($appData->account_status == 0) {

            $appData->account_status = 1;
            $appData->acc_approve_by = Auth::user()->id;
            $appData->acc_approve_at = Carbon::now();
            $appData->save();

            //cost of good sold waste qty transaction

            $detailsData = ProductionKilnDetails::where('kiln_section_id',$id)->get();

            $totalWastePrice = 0;

            foreach ($detailsData as $row_data)
            {
                $totalWastePrice = $totalWastePrice + ( $row_data->dry_westage_qty*$row_data->unit_price);
            }

            costOfGoodSoldProductionVoucher($jr_data_id,$totalWastePrice);
        }
    }
}
