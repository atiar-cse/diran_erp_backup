<?php

namespace App\Http\Controllers\Purchase;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseRequisition;
use App\Model\Purchase\PurchaseRequisitionDetails;


use Illuminate\Support\Facades\Auth;
use DB;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Purchase\PurchaseOrderReceive;
use App\Model\Purchase\PurchaseOrderReceiveDetails;

use App\Model\Accounts\AccountsPurchaseOrderVoucher;
use App\Model\Accounts\AccountsPurchaseOrderVoucherDetails;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\CheckBookLeafGenDetails;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Lib\Enumerations\StockTransactionType;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        if($request->token_req_list_gen==1){
            $requisitionLists   = PurchaseRequisition::where('status', '1')
                ->where('approve_status', '1')->where('stock_enrty_status', '0')->get();
            return $requisitionLists;
        }

        if ($request->ajax()) {
            $query = DB::table('purchase_requisitions')
                ->leftJoin('users as ura', 'purchase_requisitions.created_by','=','ura.id')
                ->leftJoin('users as ured', 'purchase_requisitions.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'purchase_requisitions.approve_by','=','urea.id')
                ->whereNull('purchase_requisitions.deleted_at')
                ->select(['purchase_requisitions.id AS id',
                    'purchase_requisitions.purchase_requisition_no',
                    'purchase_requisitions.purchase_requisition_date',
                    'purchase_requisitions.purchase_requisition_total_qty',
                    'purchase_requisitions.purchase_requisition_total_price',
                    'purchase_requisitions.purchase_requisition_narration',
                    'purchase_requisitions.purchase_requisition_supervisor_narration',
                    'purchase_requisitions.created_by',
                    'purchase_requisitions.updated_by',
                    'purchase_requisitions.approve_by',
                    'purchase_requisitions.approve_status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $EnumVal =$this->EnumVal();
        $productLists = ProductionProducts::where('production_products.status', '1')
            // ->where('production_products.is_raw_material_status', '1')
            ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
            ->Select('production_products.id','production_products.buying_price',
                    'production_products.product_name', 
                'production_measure_units.measure_unit')
            ->get();
        return view('purchase.purchase_section.purchase_requisition',compact('productLists','EnumVal'));
    }


    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $inv_no_like = 'PREQ';
        $rtn_val =InvStore($request->purchase_requisition_no, $inv_no_like,
            'purchase_requisitions','purchase_requisition_no');

        $request->merge(['purchase_requisition_no' => $rtn_val]) ;



        $this->validate($request, [
            'purchase_requisition_no'       => 'required|unique:purchase_requisitions,purchase_requisition_no',
            'purchase_requisition_date'     => 'required',

            'details.*.pr_product_id'       => 'required',
            'details.*.product_type'        => 'required',
            'details.*.pr_qty'              => 'required',
            'details.*.pr_unit_price'       => 'required',
        ], [
            'details.*.pr_product_id.required'      => 'Required field.',
            'details.*.product_type.required'       => 'Required field.',
            'details.*.pr_qty.required'             => 'Required field.',
            'details.*.pr_unit_price.required'      => 'Required field.',
        ]);


        if($request->purchase_requisition_date!=''){
            $date = str_replace('/', '-', $request->purchase_requisition_date);
            $date_val = date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }


        $app= $request->approve;

        $data = [
            'purchase_requisition_no'                   => $request->purchase_requisition_no,
            'purchase_requisition_date'                 => $date_val,
            'purchase_requisition_narration'            => $request->purchase_requisition_narration,
            'purchase_requisition_supervisor_narration' => $request->purchase_requisition_supervisor_narration,
            'purchase_requisition_total_qty'            => $request->purchase_requisition_total_qty,
            'purchase_requisition_total_price'          => $request->purchase_requisition_total_price,
            'created_by'                                => Auth::user()->id,
        ];


        try {
            DB::beginTransaction();

            $result = PurchaseRequisition::create($data);
            $details = $this->dataFormat($request, $result->id);
            PurchaseRequisitionDetails::insert($details);

            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('purchase_requisitions')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            //return response()->json(['status' => 'success', 'message' => 'Purchase Requisition successfully saved!']);
            return response()->json(['status' => 'success', 'message' => "Purchase Requisition # " . $request->purchase_requisition_no .' Successfully Saved!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show(Request $request,$id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'unapprove') {
                
                //if (Auth::user()->email == 'software@iventurebd.com') {
                    $this->unApprovePurchaseReqAndRelatedInfo($id); 
                    return response()->json(['status' => 'success', 'message' => 'Successfully UnApproved!']);
                    exit;
                //}
            }
        }

        try {
            $showData = PurchaseRequisition::FindOrFail($id);
            $showDetailsData = PurchaseRequisitionDetails::with('get_req','get_product')
                ->where('purchase_requisition_details.pr_requisition_id',$id)
                ->get();

            $data = [
                'id'    => $showData->id,
                'purchase_requisition_no'                   => $showData->purchase_requisition_no,
                'purchase_requisition_date'                 => date('d/m/Y', strtotime($showData->purchase_requisition_date)),
                'purchase_requisition_narration'            => $showData->purchase_requisition_narration,
                'purchase_requisition_supervisor_narration' => $showData->purchase_requisition_supervisor_narration,
                'purchase_requisition_total_price'          => $showData->purchase_requisition_total_price,
                'purchase_requisition_total_qty'            => $showData->purchase_requisition_total_qty,
                'approve'                                   => $showData->approve_status,
                'total_pricein_word' => getCurrency($showData->purchase_requisition_total_price),
                'details'    => $showDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        } 
    }

    public function edit($id)
    {
        try {
            $editModeData = PurchaseRequisition::FindOrFail($id);
            $editModeDetailsData = PurchaseRequisitionDetails::where('pr_requisition_id',$id)->get();
            $data = [
                'id'                                        => $editModeData->id,
                'purchase_requisition_no'                   => $editModeData->purchase_requisition_no,
                'purchase_requisition_date'                 => date('d/m/Y', strtotime($editModeData->purchase_requisition_date)),
                'purchase_requisition_narration'            => $editModeData->purchase_requisition_narration,
                'purchase_requisition_supervisor_narration' => $editModeData->purchase_requisition_supervisor_narration,
                'purchase_requisition_total_price'          => $editModeData->purchase_requisition_total_price,
                'purchase_requisition_total_qty'            => $editModeData->purchase_requisition_total_qty,
                'approve'                                   => $editModeData->approve_status,
                'deleteID'                                  => [],
                'details'                                   => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'purchase_requisition_no' => 'required|unique:purchase_requisitions,purchase_requisition_no,'.$id,'id',
            'purchase_requisition_date' => 'required',
            'details.*.pr_product_id' => 'required',
            'details.*.product_type' => 'required',
            'details.*.pr_qty' => 'required',
            'details.*.pr_unit_price' => 'required',
        ], [
            'details.*.pr_product_id.required' => 'Required field.',
            'details.*.product_type.required' => 'Required field.',
            'details.*.pr_qty.required' => 'Required field.',
            'details.*.pr_unit_price.required' => 'Required field.',
        ]);

        date_default_timezone_set("Asia/Dhaka");
        $date = str_replace('/', '-', $request->purchase_requisition_date);
        $date_val =date('Y-m-d', strtotime($date));

        $app= $request->approve;


        try {
            DB::beginTransaction();

            $editModeData = PurchaseRequisition::FindOrFail($id);

            $editModeData->purchase_requisition_no                   = $request->purchase_requisition_no;
            $editModeData->purchase_requisition_date                 = $date_val;
            $editModeData->purchase_requisition_narration            = $request->purchase_requisition_narration;
            $editModeData->purchase_requisition_supervisor_narration = $request->purchase_requisition_supervisor_narration;
            $editModeData->purchase_requisition_total_qty            = $request->purchase_requisition_total_qty;
            $editModeData->purchase_requisition_total_price          = $request->purchase_requisition_total_price;


            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            //// details transection ////
            if (count($request->deleteID) > 0) {
                PurchaseRequisitionDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'pr_requisition_id'  => $id,
                        'pr_product_id'      => $request->details[$i]['pr_product_id'],
                        'pr_qty'             => $request->details[$i]['pr_qty'],
                        'pr_unit'            => $request->details[$i]['pr_unit'],
                        'pr_unit_price'      => $request->details[$i]['pr_unit_price'],
                        'pr_total_price'     => $request->details[$i]['pr_total_price'],
                        'pr_remarks'         => $request->details[$i]['pr_remarks'],
                        'product_type'       => $request->details[$i]['product_type']
                    ];
                    PurchaseRequisitionDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'pr_requisition_id' => $id,
                        'pr_product_id'     => $request->details[$i]['pr_product_id'],
                        'pr_qty'            => $request->details[$i]['pr_qty'],
                        'pr_unit'           => $request->details[$i]['pr_unit'],
                        'pr_unit_price'     => $request->details[$i]['pr_unit_price'],
                        'pr_total_price'    => $request->details[$i]['pr_total_price'],
                        'pr_remarks'        => $request->details[$i]['pr_remarks'],
                        'product_type'      => $request->details[$i]['product_type']
                    ];
                }
            }
            if(count($dataFormat) > 0){
                PurchaseRequisitionDetails::insert($dataFormat);
            }

            ///////////////////////////////////// Approve /////////////////////////////////////////
            if($app ==1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Purchase Requisition successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{

            PurchaseRequisitionDetails::where('pr_requisition_id',$id)->delete();
            PurchaseRequisition ::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Requistion successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $approval = DB::table('purchase_requisitions')->where('id',$id)->first()->approve_status;
        if($approval == 0)
        {
            DB::table('purchase_requisitions')->where('id', $id)->update(array(
                'approve_status'    => 1,
                'approve_by'        => Auth::user()->id,
                'approve_at'        => Carbon::now(),
            ));
        }
    }

    public function PrReqNoGenerate(){
        $id = PurchaseRequisition::withTrashed()->count();
        $currentId = $id+1;
        return 'PREQ'.date('Y').date('m').$currentId;
    }

    private function EnumVal(){
        $table='purchase_requisition_details';
        $name='product_type';
        $type = DB::select( DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$name.'"') )[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = array();
        foreach(explode(',', $matches[1]) as $value){
            $v = trim( $value, "'" );
            $enum[] = $v;
        }
        return $enum;
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'pr_requisition_id' => $id,
                'pr_product_id' => $request->details[$i]['pr_product_id'],
                'pr_qty' => $request->details[$i]['pr_qty'],
                'pr_unit' => $request->details[$i]['pr_unit'],
                'pr_unit_price' => $request->details[$i]['pr_unit_price'],
                'pr_total_price' => $request->details[$i]['pr_total_price'],
                'pr_remarks' => $request->details[$i]['pr_remarks'],
                'product_type' => $request->details[$i]['product_type']
            ];
        }
        return $dataFormat;
    }

    private function unApprovePurchaseReqAndRelatedInfo($id) {
        /*
            @ Do unapproved Purchase Requisition Data
            @ Decrese Stock Entry Qty - if approved already
            @ Remove PO Receive & Stock Entry Data
            @ Remove Stock Transaction Log Data - if approved already
            @ Do reverse Purchase Invoice Voucher amounts, and remove it - if approved already
        */
        try{
            DB::beginTransaction();

            $purchaseReq = PurchaseRequisition::where('approve_status',1)->FindOrFail($id);

            $purchaseOrderRec = PurchaseOrderReceive::where('po_requisition_id',$purchaseReq->id)
                                            ->First();
                /* Check first - if accounts voucher already created and approved */
                if ($purchaseOrderRec) {
                    $accVoucher = AccountsPurchaseOrderVoucher::where('purchase_stock_id',$purchaseOrderRec->id)
                                            ->First();

                    AccountsPurchaseOrderVoucherDetails::where('ac_pruchase_stock_voucher_id',$accVoucher->id)->delete();

                        if ($accVoucher->approve_status==1) { //Ref: unAppprovePurchaseInvoiceVoucher(
                            //Get JournalEntry Table Data
                            $journalData = AccountsJournalEntry::where('transaction_reference_id',$accVoucher->id)
                                ->where('transaction_type',AccountsTransactionType::$purchaseInvVoucer)
                                ->first();
                            //REVERSE AMOUNT AND DELETE - JournalEntryDetails Table Data
                            $allJournals = AccountsJournalEntryDetails::where('journal_entry_id',$journalData->id)->get();
                            foreach ($allJournals as $row) {
                                $amount = 0;
                                $dr_cr = Null;
                                if ($row->debit_amount && ($row->debit_amount > 0) ){
                                    $amount = $row->debit_amount;
                                    $dr_cr = 'dr';
                                } elseif($row->credit_amount && ($row->credit_amount > 0) ){
                                    $amount= $row->credit_amount;
                                    $dr_cr = 'cr';
                                }

                                reverseDrCrAccountBalanceIncDec($row->char_of_account_id, $amount,$dr_cr);

                                $row->delete();
                            }

                            $journalData->delete();  

                            CheckBookLeafGenDetails::where('id',$accVoucher->cheque_leaf)->update(['use_status'=> 0]);
                        }

                    $accVoucher->delete(); 

                }

                /* Purchase Order Recive */
                if ($purchaseOrderRec && $purchaseOrderRec->approve_status == 1) {

                    $purchaseOrderRecDetails = PurchaseOrderReceiveDetails::where('pod_order_id', $purchaseOrderRec->id)
                                                            ->get();
                    $warehouseId = $purchaseOrderRec->po_warehouse_id;   
                                                                             
                    /* Decrease Stock Information */
                    foreach ($purchaseOrderRecDetails as $val) {
                        InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->pod_product_id)
                            ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                            ->decrement('inventory_stocks_current_qty', $val->pod_qty);

                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->pod_product_id)
                            ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                            ->decrement('total_price',  $val->pod_total_price);
                    }

                    //Remove Stock Log Information
                    $mainStock = StockTransectionLog::where('stock_transection_logs_type',StockTransactionType::$purchase_entry)
                                        ->where('stock_transection_logs_ref_table_name','purchase_requisitions')
                                        ->where('stock_transection_logs_ref_table_id',$purchaseReq->id)
                                        ->where('stock_transection_logs_table_name','purchase_order_receives')
                                        ->where('stock_transection_logs_table_id',$purchaseOrderRec->id)
                                        ->First();
                        if ($mainStock) {
                            StockTransectionLogDetails::where('log_id',$mainStock->id)->delete();
                            $mainStock->delete();
                        }  

                    //Delete itselt as well
                    PurchaseOrderReceiveDetails::where('pod_order_id', $purchaseOrderRec->id)
                                                ->delete();
                    $purchaseOrderRec->delete();                                                                            
                } else if($purchaseOrderRec) {

                    PurchaseOrderReceiveDetails::where('pod_order_id', $purchaseOrderRec->id)
                                                ->delete();
                    $purchaseOrderRec->delete();
                }         

            $purchaseReq->stock_enrty_status = 0;
            $purchaseReq->approve_status = 0;
            $purchaseReq->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Yes - Successfully UnApproved!']);
        }
        catch(\Exception $e){
            DB::rollback();
            $msg = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }        
    } 

}
