<?php

namespace App\Http\Controllers\Accounts\Production;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Lib\Enumerations\StockTransactionType;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsCostCenter;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Model\Production\ProductionProducts;
use App\Model\Production\ProductionRequisitionForRm;
use App\Model\Production\ProductionRequisitionForRmDetails;
use App\Model\Production\ProductionRmRatioSetup;
use App\Model\Purchase\PurchaseWareHouse;
use App\Repositories\CommonRepositories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use DB;


class RequsitionRmVoucherController extends Controller
{
    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('production_requisition_for_rms')
                ->leftJoin('users as ura', 'production_requisition_for_rms.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_requisition_for_rms.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_requisition_for_rms.approve_by','=','urea.id')
                ->whereNull('production_requisition_for_rms.deleted_at')
                ->where('production_requisition_for_rms.approve_status',1)
                ->select(['production_requisition_for_rms.id AS id',
                    'production_requisition_for_rms.rm_requisition_no',
                    'production_requisition_for_rms.requisition_date',
                    'production_requisition_for_rms.requisition_types',
                    'production_requisition_for_rms.estimate_qty_for_production',
                    'production_requisition_for_rms.total_qty',
                    'production_requisition_for_rms.total_amount',
                    'production_requisition_for_rms.created_by',
                    'production_requisition_for_rms.updated_by',
                    'production_requisition_for_rms.approve_by',

                    'production_requisition_for_rms.account_status',
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

        return view('accounts.product_section.requisition_rm', compact('chartofAccountList'));
    }

    public function show($id)
    {
        try {
            $editModeData = ProductionRequisitionForRm::with('warehouse')->FindOrFail($id);
            $editModeDetailsData = ProductionRequisitionForRmDetails::Select('production_requisition_for_rm_details.*','production_products.product_name','production_products.buying_price','production_measure_units.measure_unit')
                ->leftJoin('production_products', 'production_requisition_for_rm_details.product_id', '=', 'production_products.id')
                ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->where('requisition_for_rm_id',$id)->get();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"ReqforRm-$id")
                                ->select('id')
                                ->first();
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
                'rm_requisition_no' => $editModeData->rm_requisition_no,
                'requisition_date' => dateConvertDBtoForm($editModeData->requisition_date),
                'warehouse_id' => $editModeData->warehouse->purchase_ware_houses_name,
                'requisition_types' => $editModeData->requisition_types,
                'narration' => $editModeData->narration,
                'estimate_qty_for_production' => $editModeData->estimate_qty_for_production,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'approve_status' => $editModeData->approve_status,
                'account_status' => $editModeData->account_status,
                'details'    => $editModeDetailsData,

                'dr_coa_code'    => $dr_coa_code,
                'dr_coa_title'    => $dr_coa_title,
                'cr_coa_code'    => $cr_coa_code,
                'cr_coa_title'    => $cr_coa_title,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            echo $e->getMessage();
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = ProductionRequisitionForRm::with('warehouse')->FindOrFail($id);
            $editModeDetailsData = ProductionRequisitionForRmDetails::Select('production_requisition_for_rm_details.*','production_products.product_name','production_products.buying_price','production_measure_units.measure_unit')
                ->leftJoin('production_products', 'production_requisition_for_rm_details.product_id', '=', 'production_products.id')
                ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->where('requisition_for_rm_id',$id)->get();

            $debit_account_id = '';
            $credit_account_id = '';

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"ReqforRm-$id")->first();
            if ($jr_data) {
                $narration = $jr_data->narration;

                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $debit_account_id = $row->char_of_account_id;
                    }
                    if ($row->credit_amount > 0) {
                        $credit_account_id = $row->char_of_account_id;
                    }
                }
            }else{
                $debit_account_id = '';
                $credit_account_id = '';
                $narration = $editModeData->narration;
            }

            $data = [
                'id'                => $editModeData->id,
                'rm_requisition_no' => $editModeData->rm_requisition_no,
                'requisition_date'  => dateConvertDBtoForm($editModeData->requisition_date),
                'warehouse_id'      => $editModeData->warehouse->purchase_ware_houses_name,
                'requisition_types' => $editModeData->requisition_types,


                'estimate_qty_for_production' => $editModeData->estimate_qty_for_production,
                'total_qty'         => $editModeData->total_qty,
                'total_amount'      => $editModeData->total_amount,
                'approve_status'    => $editModeData->approve_status,
                'account_status'    => $editModeData->account_status,
                'details'           => $editModeDetailsData,

                'narration'         => $narration,

                'debit_account_id'  => $debit_account_id,
                'credit_account_id' => $credit_account_id,

            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            print_r($e->getMessage());
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
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
            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"ReqforRm-$id")->first();
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

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"ReqforRm-$id")->first();
                $jr_data->approve_status = 1;
                $jr_data->save();

                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                $this->commonRepositories->currentBalanceIncrementDecrement($jr_details);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'The Requisition Form was successfully updated!']);
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
            'transaction_reference_no'  => "ReqforRm-$id",
            'transaction_date'          => dateConvertFormtoDB($request->requisition_date),
            'transaction_type'          => AccountsTransactionType::$ProductionReqRm,
            'transaction_type_name'     => AccountsTransactionType::$ProductionReqRmTitle,

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
        $appData = ProductionRequisitionForRm::FindOrFail($id);
        if ($appData->account_status == 0) {

            $appData->account_status = 1;
            $appData->acc_approve_by = Auth::user()->id;
            $appData->acc_approve_at = Carbon::now();
            $appData->save();
        }
    }
}
