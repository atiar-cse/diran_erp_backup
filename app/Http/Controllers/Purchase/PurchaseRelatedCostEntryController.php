<?php

namespace App\Http\Controllers\Purchase;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseOrderReceiveDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Purchase\PurchaseCostType;
use App\Model\Purchase\PurchaseOrderReceive;
use App\Model\Purchase\PurchaseRelatedCostEntry;
use App\Model\Purchase\PurchaseRelatedCostEntryDetails;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;

use Illuminate\Support\Facades\Auth;
use DB;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Repositories\CommonRepositories;

class PurchaseRelatedCostEntryController extends Controller
{
    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('purchase_related_cost_entries')
                ->leftJoin('purchase_order_receives', 'purchase_related_cost_entries.po_order_receive_id', '=', 'purchase_order_receives.id')
                ->leftJoin('users as ura', 'purchase_related_cost_entries.created_by','=','ura.id')
                ->leftJoin('users as ured', 'purchase_related_cost_entries.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'purchase_related_cost_entries.approve_by','=','urea.id')
                ->whereNull('purchase_related_cost_entries.deleted_at')
                ->select(['purchase_related_cost_entries.id AS id',
                    'purchase_related_cost_entries.po_cost_date',
                    'purchase_related_cost_entries.po_cost_narration',
                    'purchase_related_cost_entries.po_total_cost',
                    'purchase_related_cost_entries.approve_status',
                    'purchase_related_cost_entries.created_by',
                    'purchase_related_cost_entries.updated_by',
                    'purchase_related_cost_entries.approve_by',
                    'purchase_order_receives.po_order_no',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

       // DB::enableQueryLog();
        $ids=  PurchaseRelatedCostEntry::where('approve_status',1)->pluck('po_order_receive_id');

        $invoiceLists = PurchaseOrderReceive::where('status', '1')
                                ->where('approve_status','1')
                                ->where('purchase_order_receives.account_status',0)
                                ->whereNotIn('id',$ids)->get();
        //print_r(DB::getQueryLog($invoiceLists));

        $costTypeLists   = PurchaseCostType::where('status', '1')->get();
        $chartAcLists   = AccountsChartofAccounts::where('status', '1')->get();
        return view('purchase.purchase_section.purchase_related_cost_entry',compact(
                        'invoiceLists','costTypeLists','chartAcLists'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'po_order_receive_id' => 'required',
            'po_cost_date' => 'required',
            'details.*.po_cost_type_id' => 'required',
            'details.*.po_cost_amount' => 'required',
        ], [
            'details.*.po_cost_type_id.required' => 'Required field.',
            'details.*.po_cost_amount.required' => 'Required field.',
        ]);

        if($request->po_cost_date!=''){
            $date = str_replace('/', '-', $request->po_cost_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $approve = $request->approve;
        $data = [
            'po_order_receive_id' => $request->po_order_receive_id,
            'po_cost_date' => $date_val,
            'po_cost_narration' => $request->po_cost_narration,
            'po_total_cost' => $request->po_total_cost,
            'credit_ac_id' => $request->credit_ac_id,
            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = PurchaseRelatedCostEntry::create($data);
            $details = $this->dataFormat($request, $result->id);

            PurchaseRelatedCostEntryDetails::insert($details);

            if($approve == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('purchase_related_cost_entries')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Purchase Requisition successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {

        try {
            $showData = PurchaseRelatedCostEntry::FindOrFail($id);
            $showDetailsData = PurchaseRelatedCostEntryDetails::with('get_type')
                ->where('purchase_related_cost_entry_details.po_cost_id',$id)
                ->get();

            $data = [
                'id'                    => $showData->id,
                'po_order_receive_id'   => PurchaseOrderReceive::where('id',$showData->po_order_receive_id)->first()->po_order_no,
                'po_cost_date'          => date('d/m/Y', strtotime($showData->po_cost_date)),
                'po_cost_narration'     => $showData->po_cost_narration,
                'po_total_cost'         => $showData->po_total_cost,
                'credit_ac_id'          => AccountsChartofAccounts::where('id',$showData->credit_ac_id)->first()->chart_of_accounts_title,
                'approve'               => $showData->approve_status,
                'details'               => $showDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = PurchaseRelatedCostEntry::FindOrFail($id);
            $editModeDetailsData = PurchaseRelatedCostEntryDetails::where('po_cost_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'po_order_receive_id' => $editModeData->po_order_receive_id,
                'po_cost_date' => date('d/m/Y', strtotime($editModeData->po_cost_date)),
                'po_cost_narration' => $editModeData->po_cost_narration,
                'po_total_cost' => $editModeData->po_total_cost,
                'credit_ac_id' => $editModeData->credit_ac_id,
                'approve' => '',
                'deleteID' => [],
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'po_order_receive_id' => 'required',
            'po_cost_date' => 'required',
            'details.*.po_cost_type_id' => 'required',
            'details.*.po_cost_amount' => 'required',
        ], [
            'details.*.po_cost_type_id.required' => 'Required field.',
            'details.*.po_cost_amount.required' => 'Required field.',
        ]);

        if($request->po_cost_date!= ''){
            $date = str_replace('/', '-', $request->po_cost_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $approve = $request->approve;

        try {
            DB::beginTransaction();

            $editModeData = PurchaseRelatedCostEntry::FindOrFail($id);

            $editModeData->po_order_receive_id = $request->po_order_receive_id;
            $editModeData->po_cost_date        = $date_val;
            $editModeData->po_cost_narration   = $request->po_cost_narration;
            $editModeData->po_total_cost       = $request->po_total_cost;
            $editModeData->credit_ac_id        = $request->credit_ac_id;

            if ($approve!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();
            //// details transection ////
            if (count($request->deleteID) > 0) {
                PurchaseRelatedCostEntryDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'po_cost_id' => $id,
                        'po_cost_type_id' => $request->details[$i]['po_cost_type_id'],
                        'remarks' => $request->details[$i]['remarks'],
                        'po_cost_amount' => $request->details[$i]['po_cost_amount'],
                    ];
                    PurchaseRelatedCostEntryDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'po_cost_id' => $id,
                        'po_cost_type_id' => $request->details[$i]['po_cost_type_id'],
                        'remarks' => $request->details[$i]['remarks'],
                        'po_cost_amount' => $request->details[$i]['po_cost_amount']
                    ];
                }
            }

            if(count($dataFormat) > 0){
                PurchaseRelatedCostEntryDetails::insert($dataFormat);
            }

            if($approve == 1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Purchase Cost Entry successfully updated!']);
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id){

        $approve_status = DB::table('purchase_related_cost_entries')->where('id',$id)->first()->approve_status;

        if($approve_status == 0) {
            $appData = PurchaseRelatedCostEntry::FindOrFail($id);

            $appDetailsData = DB::table('purchase_related_cost_entry_details as co_dt')
                                ->leftJoin('purchase_cost_types as co_tp', 'co_dt.po_cost_type_id', '=' , 'co_tp.id')
                                ->select('co_dt.*','co_tp.chart_ac_id')
                                ->get();

            $cost_entry_amount = $appData->po_total_cost;
            $date = $appData->po_cost_date;

            /*Inventory price Update*/
            $purOrderRec = PurchaseOrderReceive::where('id',$appData->po_order_receive_id)->first();
            $new_cost_unit_price = round( ($cost_entry_amount / $purOrderRec->po_total_qty) , 2 ); // New Price Per Qty to Add
            $warehouseId = $purOrderRec->po_warehouse_id;

            $purchaseProducts = PurchaseOrderReceiveDetails::where('pod_order_id',$appData->po_order_receive_id)
                                    ->get();

            foreach ($purchaseProducts as $pid){

                $new_extra_total_price = $pid->pod_qty * $new_cost_unit_price;

                // InventoryCurrentStock::where('inventory_current_stocks_product_id',$pid->pod_product_id)
                //     ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                //     ->increment('total_price', $new_extra_total_price);

                $Ics = InventoryCurrentStock::where('inventory_current_stocks_product_id', $pid->pod_product_id)
                            ->where('inventory_current_stocks_warehouse_id', $warehouseId)->first();

                $new_total_price = round( $Ics->total_price + $new_extra_total_price, 2);
                $new_avg_unit_price = round( $new_total_price / $Ics->inventory_stocks_current_qty, 2);

                InventoryCurrentStock::where('inventory_current_stocks_product_id', $pid->pod_product_id)
                    ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->update(array(
                        'unit_price'    => $new_avg_unit_price,
                        'total_price'   => $new_total_price,
                    ));
            }

            /******************************************** Journal Entry ****************************************************/
            $jid = AccountsJournalEntry::count();
            $currentId = $jid+1;
            $journalRef='JPRC-'.date('Ym').$currentId;

            $data_jr = [
                'transaction_reference_id'      => $id,
                'transaction_reference_no'      => $journalRef,
                'transaction_date'              => $date,
                'transaction_type'              => AccountsTransactionType::$purchaseCostEntry,
                'transaction_type_name'         => 'Journal Purchase Related Cost',

                'cost_center_id'                => '',
                'branch_id'                     => '',

                'narration'                     => $appData->po_cost_narration,
                'total_debit'                   => $cost_entry_amount,
                'total_credit'                  => $cost_entry_amount,
                'approve_status'                => 1,
                'created_by'                    => Auth::user()->id,
            ];
            $result_jr = AccountsJournalEntry::create($data_jr);

            $dataFormat = [];
            $i=0;
            foreach($appDetailsData as $dt){
                $dataFormat[$i] = [
                    'journal_entry_id'      => $result_jr->id,
                    'char_of_account_id'    => $dt->chart_ac_id,
                    'remarks'               => 'Debit',
                    'debit_amount'          => $dt->po_cost_amount,
                    'credit_amount'         => '',
                ];
                $i++;
            }
            $dataFormat[$i] = [
                'journal_entry_id'       => $result_jr->id,
                'char_of_account_id'    => $appData['credit_ac_id'],
                'remarks'               => 'Credit',
                'debit_amount'          => '',
                'credit_amount'         => $appData['po_total_cost'],
            ];

            AccountsJournalEntryDetails::insert($dataFormat);
            $this->commonRepositories->currentBalanceIncrementDecrement($dataFormat);

            /****************************************** APPROVE INFORMATION  **********************************************************/
            DB::table('purchase_related_cost_entries')->where('id', $id)->update(array(
                'approve_status'    => 1,
                'approve_by'        => Auth::user()->id,
                'approve_at'        => Carbon::now(),
            ));
        }
    }

    public function destroy($id)
    {
        try{
            PurchaseRelatedCostEntryDetails::where('po_cost_id',$id)->delete();
            PurchaseRelatedCostEntry::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Cost Entry successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'po_cost_id' => $id,
                'po_cost_type_id' => $request->details[$i]['po_cost_type_id'],
                'remarks' => $request->details[$i]['remarks'],
                'po_cost_amount' => $request->details[$i]['po_cost_amount']
            ];
        }

        return $dataFormat;
    }
}
